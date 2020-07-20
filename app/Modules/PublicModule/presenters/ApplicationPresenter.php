<?php

namespace FKSDB\Modules\PublicModule;

use Authorization\RelatedPersonAuthorizator;
use FKSDB\Config\NeonSchemaException;
use FKSDB\Events\Machine\BaseMachine;
use FKSDB\Events\Machine\Machine;
use FKSDB\Events\Model\ApplicationHandlerFactory;
use FKSDB\Events\Model\Grid\InitSource;
use FKSDB\Events\Model\Grid\RelatedPersonSource;
use FKSDB\Events\Model\Holder\Holder;
use FKSDB\Components\Controls\ContestChooser;
use FKSDB\Components\Events\ApplicationComponent;
use FKSDB\Components\Events\ApplicationsGrid;
use FKSDB\Components\Grids\Events\LayoutResolver;
use FKSDB\Events\EventDispatchFactory;
use FKSDB\Exceptions\BadTypeException;
use FKSDB\Exceptions\GoneException;
use FKSDB\Exceptions\NotFoundException;
use FKSDB\Exceptions\NotImplementedException;
use FKSDB\Localization\UnsupportedLanguageException;
use FKSDB\Logging\MemoryLogger;
use FKSDB\ORM\AbstractModelMulti;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\IModel;
use FKSDB\ORM\Models\Fyziklani\ModelFyziklaniTeam;
use FKSDB\ORM\Models\IEventReferencedModel;
use FKSDB\ORM\Models\ModelAuthToken;
use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Models\ModelEventParticipant;
use FKSDB\ORM\Services\ServiceEvent;
use FKSDB\UI\PageTitle;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\InvalidArgumentException;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ApplicationPresenter extends BasePresenter {

    const PARAM_AFTER = 'a';

    /** @var ModelEvent|null */
    private $event;

    /** @var IModel|ModelFyziklaniTeam|ModelEventParticipant */
    private $eventApplication = false;

    /** @var Holder */
    private $holder;

    /** @var Machine */
    private $machine;

    /** @var ServiceEvent */
    private $serviceEvent;

    /** @var RelatedPersonAuthorizator */
    private $relatedPersonAuthorizator;

    /** @var LayoutResolver */
    private $layoutResolver;

    /** @var ApplicationHandlerFactory */
    private $handlerFactory;
    /** @var EventDispatchFactory */
    private $eventDispatchFactory;

    /**
     * @param ServiceEvent $serviceEvent
     * @return void
     */
    public function injectServiceEvent(ServiceEvent $serviceEvent) {
        $this->serviceEvent = $serviceEvent;
    }

    /**
     * @param RelatedPersonAuthorizator $relatedPersonAuthorizator
     * @return void
     */
    public function injectRelatedPersonAuthorizator(RelatedPersonAuthorizator $relatedPersonAuthorizator) {
        $this->relatedPersonAuthorizator = $relatedPersonAuthorizator;
    }

    /**
     * @param LayoutResolver $layoutResolver
     * @return void
     */
    public function injectLayoutResolver(LayoutResolver $layoutResolver) {
        $this->layoutResolver = $layoutResolver;
    }

    /**
     * @param ApplicationHandlerFactory $handlerFactory
     * @return void
     */
    public function injectHandlerFactory(ApplicationHandlerFactory $handlerFactory) {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @param EventDispatchFactory $eventDispatchFactory
     * @return void
     */
    public function injectEventDispatchFactory(EventDispatchFactory $eventDispatchFactory) {
        $this->eventDispatchFactory = $eventDispatchFactory;
    }

    /**
     * @param int $eventId
     * @param int $id
     * @throws GoneException
     */
    public function authorizedDefault($eventId, $id) {
        /** @var ModelEvent $event */
        $event = $this->getEvent();
        if ($this->contestAuthorizator->isAllowed('event.participant', 'edit', $event->getContest())
            || $this->contestAuthorizator->isAllowed('fyziklani.team', 'edit', $event->getContest())) {
            $this->setAuthorized(true);
            return;
        }
        if (strtotime($event->registration_begin) > time() || strtotime($event->registration_end) < time()) {
            throw new GoneException();
        }
    }

    public function authorizedList() {
        $this->setAuthorized($this->getUser()->isLoggedIn() && $this->getUser()->getIdentity()->getPerson());
    }

    /**
     * @throws \Throwable
     */
    public function titleDefault() {
        if ($this->getEventApplication()) {
            $this->setPageTitle(new PageTitle(\sprintf(_('Application for %s: %s'), $this->getEvent()->name, $this->getEventApplication()->__toString()), 'fa fa-calendar-check-o'));
        } else {
            $this->setPageTitle(new PageTitle($this->getEvent(), 'fa fa-calendar-check-o'));
        }
    }

    /**
     * @throws BadTypeException
     * @throws ForbiddenRequestException
     */
    public function titleList() {
        $contest = $this->getSelectedContest();
        if ($contest) {
            $this->setPageTitle(new PageTitle(\sprintf(_('Moje přihlášky (%s)'), $contest->name), 'fa fa-calendar'));
        } else {
            $this->setPageTitle(new PageTitle(_('Moje přihlášky'), 'fa fa-calendar'));
        }
    }

    /**
     * @return void
     * @throws ForbiddenRequestException
     * @throws NeonSchemaException
     */
    protected function unauthorizedAccess() {
        if ($this->getAction() == 'default') {
            $this->initializeMachine();
            if ($this->getHolder()->getPrimaryHolder()->getModelState() == BaseMachine::STATE_INIT) {
                return;
            }
        }

        parent::unauthorizedAccess();
    }

    /**
     * @return bool
     */
    public function requiresLogin() {
        return $this->getAction() != 'default';
    }

    /**
     * @param int $eventId
     * @param int $id
     *
     * @throws AbortException
     *
     * @throws BadTypeException
     * @throws ForbiddenRequestException
     * @throws NeonSchemaException
     * @throws NotFoundException
     */
    public function actionDefault($eventId, $id) {
        if (!$this->getEvent()) {
            throw new NotFoundException(_('Neexistující akce.'));
        }
        $eventApplication = $this->getEventApplication();
        if ($id) { // test if there is a new application, case is set there are a edit od application, empty => new application
            if (!$eventApplication) {
                throw new NotFoundException(_('Neexistující přihláška.'));
            }
            if (!$eventApplication instanceof IEventReferencedModel) {
                throw new BadTypeException(IEventReferencedModel::class, $eventApplication);
            }
            if ($this->getEvent()->event_id !== $eventApplication->getEvent()->event_id) {
                throw new ForbiddenRequestException();
            }
        }

        $this->initializeMachine();

        if ($this->getTokenAuthenticator()->isAuthenticatedByToken(ModelAuthToken::TYPE_EVENT_NOTIFY)) {
            $data = $this->getTokenAuthenticator()->getTokenData();
            if ($data) {
                $this->getTokenAuthenticator()->disposeTokenData();
                $this->redirect('this', self::decodeParameters($data));
            }
        }


        if (!$this->getMachine()->getPrimaryMachine()->getAvailableTransitions($this->holder, $this->getHolder()->getPrimaryHolder()->getModelState())) {

            if ($this->getHolder()->getPrimaryHolder()->getModelState() == BaseMachine::STATE_INIT) {
                $this->setView('closed');
                $this->flashMessage(_('Přihlašování není povoleno.'), BasePresenter::FLASH_INFO);
            } elseif (!$this->getParameter(self::PARAM_AFTER, false)) {
                $this->flashMessage(_('Automat přihlášky nemá aktuálně žádné možné přechody.'), BasePresenter::FLASH_INFO);
            }
        }

        if (!$this->relatedPersonAuthorizator->isRelatedPerson($this->getHolder()) && !$this->getContestAuthorizator()->isAllowed($this->getEvent(), 'application', $this->getEvent()->getContest())) {
            if ($this->getParameter(self::PARAM_AFTER, false)) {
                $this->setView('closed');
            } else {
                $this->loginRedirect();
            }
        }
    }

    /**
     * @throws BadTypeException
     * @throws ForbiddenRequestException
     */
    public function actionList() {
        if (!$this->getSelectedContest()) {
            $this->setView('contestChooser');
        }
    }

    /**
     * @return void
     * @throws NeonSchemaException
     */
    private function initializeMachine() {
        $this->getHolder()->setModel($this->getEventApplication());
    }

    /**
     * @return ContestChooser
     * @throws NotFoundException
     */
    protected function createComponentContestChooser(): ContestChooser {
        $component = parent::createComponentContestChooser();
        if ($this->getAction() == 'default') {
            if (!$this->getEvent()) {
                throw new NotFoundException(_('Neexistující akce.'));
            }
            $component->setContests([
                $this->getEvent()->getEventType()->contest_id,
            ]);
        } elseif ($this->getAction() == 'list') {
            $component->setContests(ContestChooser::CONTESTS_ALL);
        }
        return $component;
    }

    /**
     * @return ApplicationComponent
     * @throws NeonSchemaException
     * @throws NotImplementedException
     */
    protected function createComponentApplication() {
        $logger = new MemoryLogger();
        $handler = $this->handlerFactory->create($this->getEvent(), $logger);
        $component = new ApplicationComponent($this->getContext(), $handler, $this->getHolder());
        $component->setRedirectCallback(function ($modelId, $eventId) {
            $this->backLinkRedirect();
            $this->redirect('this', [
                'eventId' => $eventId,
                'id' => $modelId,
                self::PARAM_AFTER => true,
            ]);
        });
        $component->setTemplate($this->layoutResolver->getFormLayout($this->getEvent()));
        return $component;
    }

    /**
     * @return ApplicationsGrid
     *
     * @throws BadTypeException
     * @throws ForbiddenRequestException
     */
    protected function createComponentApplicationsGrid() {
        $person = $this->getUser()->getIdentity()->getPerson();
        $events = $this->serviceEvent->getTable();
        $events->where('event_type.contest_id', $this->getSelectedContest()->contest_id);

        $source = new RelatedPersonSource($person, $events, $this->getContext());

        $grid = new ApplicationsGrid($this->getContext(), $source, $this->handlerFactory);

        $grid->setTemplate('myApplications');

        return $grid;
    }

    /**
     * @return ApplicationsGrid
     *
     * @throws BadTypeException
     * @throws ForbiddenRequestException
     */
    protected function createComponentNewApplicationsGrid() {
        $events = $this->serviceEvent->getTable();
        $events->where('event_type.contest_id', $this->getSelectedContest()->contest_id)
            ->where('registration_begin <= NOW()')
            ->where('registration_end >= NOW()');

        $source = new InitSource($events, $this->getContext(), $this->eventDispatchFactory);
        $grid = new ApplicationsGrid($this->getContext(), $source, $this->handlerFactory);
        $grid->setTemplate('myApplications');

        return $grid;
    }

    /**
     * @return ModelEvent|null
     */
    private function getEvent() {
        if (!$this->event) {
            $eventId = null;
            if ($this->getTokenAuthenticator()->isAuthenticatedByToken(ModelAuthToken::TYPE_EVENT_NOTIFY)) {
                $data = $this->getTokenAuthenticator()->getTokenData();
                if ($data) {
                    $data = self::decodeParameters($this->getTokenAuthenticator()->getTokenData());
                    $eventId = $data['eventId'];
                }
            }
            $eventId = $eventId ?: $this->getParameter('eventId');
            $event = $this->serviceEvent->findByPrimary($eventId);
            if ($event) {
                $this->event = $event;
            }
        }

        return $this->event;
    }

    /**
     * @return AbstractModelMulti|AbstractModelSingle|IModel|ModelFyziklaniTeam|ModelEventParticipant|IEventReferencedModel
     * @throws NeonSchemaException
     */
    private function getEventApplication() {
        if (!$this->eventApplication) {
            $id = null;
            //if ($this->getTokenAuthenticator()->isAuthenticatedByToken(ModelAuthToken::TYPE_EVENT_NOTIFY)) {
            //   $data = $this->getTokenAuthenticator()->getTokenData();
            //   if ($data) {
            //    $data = self::decodeParameters($this->getTokenAuthenticator()->getTokenData());
            //$eventId = $data['id']; // TODO $id?
            //  }
            // }
            $id = $id ?: $this->getParameter('id');
            $service = $this->getHolder()->getPrimaryHolder()->getService();

            $this->eventApplication = $service->findByPrimary($id);
            /* if ($row) {
                 $this->eventApplication = ($service->getModelClassName())::createFromActiveRow($row);
             }*/
        }

        return $this->eventApplication;
    }

    /**
     * @return Holder
     * @throws NeonSchemaException
     */
    private function getHolder() {
        if (!$this->holder) {
            $this->holder = $this->eventDispatchFactory->getDummyHolder($this->getEvent());
        }
        return $this->holder;
    }

    /**
     * @return Machine
     *
     */
    private function getMachine() {
        if (!$this->machine) {
            $this->machine = $this->eventDispatchFactory->getEventMachine($this->getEvent());
        }
        return $this->machine;
    }

    /**
     * @param int $eventId
     * @param int $id
     * @return string
     */
    public static function encodeParameters($eventId, $id) {
        return "$eventId:$id";
    }

    /**
     * @param string $data
     * @return array
     */
    public static function decodeParameters($data) {
        $parts = explode(':', $data);
        if (count($parts) != 2) {
            throw new InvalidArgumentException("Cannot decode '$data'.");
        }
        return [
            'eventId' => $parts[0],
            'id' => $parts[1],
        ];
    }

    /**
     * @return void
     * @throws BadTypeException
     * @throws UnsupportedLanguageException
     * @throws \ReflectionException
     */
    protected function beforeRender() {
        $event = $this->getEvent();
        if ($event) {
            $this->getPageStyleContainer()->styleId = ' event-type-' . $event->event_type_id;
        }
        parent::beforeRender();
    }
}
