<?php

namespace FKSDB\Models\ORM\Services;

use FKSDB\Models\ORM\Models\ModelGlobalSession;
use Nette\Http\Request;
use Nette\Utils\DateTime;
use Nette\Utils\Random;
use Fykosak\NetteORM\AbstractService;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class ServiceGlobalSession extends AbstractService {

    private const SESSION_ID_LENGTH = 32;

    public function createSession(int $loginId,Request $request, ?DateTime $until = null, ?DateTime $since = null): ModelGlobalSession {
        if ($since === null) {
            $since = new DateTime();
        }

        $this->explorer->getConnection()->beginTransaction();

        do {
            $sessionId = Random::generate(self::SESSION_ID_LENGTH, 'a-zA-Z0-9');
        } while ($this->findByPrimary($sessionId));
        /** @var ModelGlobalSession $session */
        $session = $this->createNewModel([
            'session_id' => $sessionId,
            'login_id' => $loginId,
            'since' => $since,
            'until' => $until,
            'remote_ip' => $request->getRemoteAddress(),
        ]);
        $this->explorer->getConnection()->commit();

        return $session;
    }
}
