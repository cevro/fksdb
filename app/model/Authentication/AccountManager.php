<?php

namespace FKSDB\Authentication;

use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\Models\ModelAuthToken;
use FKSDB\ORM\Models\ModelLogin;
use FKSDB\ORM\Models\ModelPerson;
use FKSDB\ORM\Services\ServiceAuthToken;
use FKSDB\ORM\Services\ServiceEmailMessage;
use FKSDB\ORM\Services\ServiceLogin;
use Mail\MailTemplateFactory;
use Mail\SendFailedException;
use Nette\Utils\DateTime;
use Nette\InvalidStateException;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Templating\ITemplate;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class AccountManager {

    /**
     * @var ServiceLogin
     */
    private $serviceLogin;

    /**
     * @var ServiceAuthToken
     */
    private $serviceAuthToken;

    /**
     * @var IMailer
     */
    private $mailer;
    private $invitationExpiration = '+1 month';
    private $recoveryExpiration = '+1 day';
    private $emailFrom;
    /**
     * @var ServiceEmailMessage
     */
    private $serviceEmailMessage;
    /**
     * @var MailTemplateFactory
     */
    private $mailTemplateFactory;

    /**
     * AccountManager constructor.
     * @param MailTemplateFactory $mailTemplateFactory
     * @param ServiceLogin $serviceLogin
     * @param ServiceAuthToken $serviceAuthToken
     * @param IMailer $mailer
     * @param ServiceEmailMessage $serviceEmailMessage
     */
    function __construct(MailTemplateFactory $mailTemplateFactory,
                         ServiceLogin $serviceLogin,
                         ServiceAuthToken $serviceAuthToken,
                         IMailer $mailer,
                         ServiceEmailMessage $serviceEmailMessage) {
        $this->serviceLogin = $serviceLogin;
        $this->serviceAuthToken = $serviceAuthToken;
        $this->mailer = $mailer;
        $this->serviceEmailMessage = $serviceEmailMessage;
        $this->mailTemplateFactory = $mailTemplateFactory;
    }

    /**
     * @return string
     */
    public function getInvitationExpiration() {
        return $this->invitationExpiration;
    }

    /**
     * @param $invitationExpiration
     */
    public function setInvitationExpiration($invitationExpiration) {
        $this->invitationExpiration = $invitationExpiration;
    }

    /**
     * @return string
     */
    public function getRecoveryExpiration() {
        return $this->recoveryExpiration;
    }

    /**
     * @param $recoveryExpiration
     */
    public function setRecoveryExpiration($recoveryExpiration) {
        $this->recoveryExpiration = $recoveryExpiration;
    }

    public function getEmailFrom() {
        return $this->emailFrom;
    }

    /**
     * @param $emailFrom
     */
    public function setEmailFrom($emailFrom) {
        $this->emailFrom = $emailFrom;
    }

    /**
     * Creates login and invites user to set up the account.
     *
     * @param ITemplate $template template of the mail
     * @param ModelPerson $person
     * @param string $email
     * @return ModelLogin
     * @throws SendFailedException
     * @throws \Exception
     */
    public function createLoginWithInvitation(ModelPerson $person, $email) {
        $login = $this->createLogin($person);
        //TODO email

        $this->serviceLogin->save($login);

        $until = DateTime::from($this->getInvitationExpiration());
        $token = $this->serviceAuthToken->createToken($login, ModelAuthToken::TYPE_INITIAL_LOGIN, $until);

        $templateParams = [
            'token' => $token->token,
            'person' => $person,
            'email' => $email,
            'until' => $until,
        ];
        $data = [];
        $data['text'] = (string)$this->mailTemplateFactory->createLoginInvitation($person->getPreferredLang(), $templateParams);
        $data['subject'] = _('Založení účtu');
        $data['sender'] = $this->getEmailFrom();
        $data['recipient'] = $email;
        $this->serviceEmailMessage->addMessageToSend($data);
        return $login;

    }

    /**
     * @param ITemplate $template
     * @param ModelLogin $login
     * @throws \Exception
     */
    public function sendRecovery(ITemplate $template, ModelLogin $login) {
        $person = $login->getPerson();
        $recoveryAddress = $person ? $person->getInfo()->email : null;
        if (!$recoveryAddress) {
            throw new RecoveryNotImplementedException();
        }
        $token = $this->serviceAuthToken->getTable()->where(array(
            'login_id' => $login->login_id,
            'type' => ModelAuthToken::TYPE_RECOVERY,
        ))
            ->where('until > ?', new DateTime())->fetch();

        if ($token) {
            throw new RecoveryExistsException();
        }

        $until = DateTime::from($this->getRecoveryExpiration());
        $token = $this->serviceAuthToken->createToken($login, ModelAuthToken::TYPE_RECOVERY, $until);

        // prepare and send email
        $template->token = $token->token;
        $template->login = $login;
        $template->until = $until;

        $message = new Message();
        $message->setHtmlBody($template);
        $message->setSubject(_('Obnova hesla'));
        $message->setFrom($this->getEmailFrom());
        $message->addTo($recoveryAddress, $login->__toString());

        try {
            $this->mailer->send($message);
        } catch (InvalidStateException $exception) {
            throw new SendFailedException($exception);
        }
    }

    /**
     * @param ModelLogin $login
     */
    public function cancelRecovery(ModelLogin $login) {
        $this->serviceAuthToken->getTable()->where(array(
            'login_id' => $login->login_id,
            'type' => ModelAuthToken::TYPE_RECOVERY,
        ))->delete();
    }

    /**
     * @param ModelPerson $person
     * @param null $login
     * @param null $password
     * @return AbstractModelSingle|ModelLogin
     */
    public final function createLogin(ModelPerson $person, $login = null, $password = null) {
        $login = $this->serviceLogin->createNew(array(
            'person_id' => $person->person_id,
            'login' => $login,
            'active' => 1,
        ));

        $this->serviceLogin->save($login);

        /* Must be done after login_id is allocated. */
        if ($password) {
            $login->setHash($password);
            $this->serviceLogin->save($login);
        }
        return $login;
    }

}
