<?php

declare(strict_types=1);

namespace FKSDB\Modules\EventModule;

use FKSDB\Models\Events\EventDispatchFactory;
use FKSDB\Models\Events\Exceptions\ConfigurationNotFoundException;
use FKSDB\Models\Events\Exceptions\EventNotFoundException;
use FKSDB\Models\Events\Model\Holder\Holder;
use FKSDB\Models\Exceptions\BadTypeException;
use FKSDB\Models\Exceptions\NotImplementedException;
use FKSDB\Models\Expressions\NeonSchemaException;
use FKSDB\Models\Localization\UnsupportedLanguageException;
use FKSDB\Models\ORM\Models\ModelContest;
use FKSDB\Models\ORM\Models\ModelEvent;
use FKSDB\Models\ORM\Services\ServiceEvent;
use FKSDB\Modules\Core\AuthenticatedPresenter;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Security\Resource;

abstract class BasePresenter extends AuthenticatedPresenter
{

    /**
     * @persistent
     */
    public ?int $eventId = null;
    protected ServiceEvent $serviceEvent;
    protected EventDispatchFactory $eventDispatchFactory;
    private ModelEvent $event;
    private Holder $holder;

    final public function injectEventBase(ServiceEvent $serviceEvent, EventDispatchFactory $eventDispatchFactory): void
    {
        $this->serviceEvent = $serviceEvent;
        $this->eventDispatchFactory = $eventDispatchFactory;
    }

    public function isAuthorized(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return parent::isAuthorized();
    }

    /**
     * @param Resource|string|null $resource
     * @param string|null $privilege
     * Check if has contest permission or is Event org
     * @throws EventNotFoundException
     */
    public function isEventOrContestOrgAuthorized($resource, ?string $privilege): bool
    {
        return $this->eventAuthorizator->isEventOrContestOrgAllowed($resource, $privilege, $this->getEvent());
    }

    /**
     * @throws NotImplementedException
     * @throws ForbiddenRequestException
     */
    protected function startup(): void
    {
        if (!$this->isEnabled()) {
            throw new NotImplementedException();
        }
        parent::startup();
    }

    protected function isEnabled(): bool
    {
        return true;
    }

    /**
     * @throws EventNotFoundException
     * @throws NeonSchemaException
     * @throws ConfigurationNotFoundException
     */
    protected function getHolder(): Holder
    {
        if (!isset($this->holder)) {
            $this->holder = $this->eventDispatchFactory->getDummyHolder($this->getEvent());
        }
        return $this->holder;
    }

    /**
     * @throws EventNotFoundException
     */
    protected function getEvent(): ModelEvent
    {
        if (!isset($this->event)) {
            $model = $this->serviceEvent->findByPrimary($this->eventId);
            if (!$model) {
                throw new EventNotFoundException();
            }
            $this->event = $model;
        }
        return $this->event;
    }

    /**
     * @throws EventNotFoundException
     */
    final protected function getContest(): ModelContest
    {
        return $this->getEvent()->getContest();
    }

    /* **************** ACL *********************** */

    /**
     * @throws EventNotFoundException
     */
    protected function isTeamEvent(): bool
    {
        return in_array($this->getEvent()->event_type_id, ModelEvent::TEAM_EVENTS);
    }

    /**
     * @param Resource|string|null $resource
     * @param string|null $privilege
     * Standard ACL from acl.neon
     * @throws EventNotFoundException
     */
    protected function isContestsOrgAuthorized($resource, ?string $privilege): bool
    {
        return $this->eventAuthorizator->isContestOrgAllowed($resource, $privilege, $this->getEvent());
    }

    /**
     * @param Resource|string|null $resource
     * @param string|null $privilege
     * Check if is contest and event org
     * TODO vyfakuje to aj cartesianov
     * @throws EventNotFoundException
     */
    protected function isEventAndContestOrgAuthorized($resource, ?string $privilege): bool
    {
        return $this->eventAuthorizator->isEventAndContestOrgAllowed($resource, $privilege, $this->getEvent());
    }

    /* ********************** GUI ************************ */

    /**
     * @throws EventNotFoundException
     */
    protected function getDefaultSubTitle(): ?string
    {
        return $this->getEvent()->__toString();
    }

    /**
     * @throws BadTypeException
     * @throws EventNotFoundException
     * @throws UnsupportedLanguageException
     * @throws BadRequestException
     * @throws \ReflectionException
     */
    protected function beforeRender(): void
    {
        $this->getPageStyleContainer()->styleId = 'event event-type-' . $this->getEvent()->event_type_id;
        switch ($this->getEvent()->event_type_id) {
            case 1:
                $this->getPageStyleContainer()->setNavBarClassName('bg-fyziklani navbar-dark');
                $this->getPageStyleContainer()->setNavBrandPath('/images/logo/white.svg');
                break;
            case 9:
                $this->getPageStyleContainer()->setNavBarClassName('bg-fol navbar-light');
                break;
            default:
                $this->getPageStyleContainer()->setNavBarClassName('bg-light navbar-light');
        }
        parent::beforeRender();
    }

    /**
     * @return string[]
     */
    protected function getNavRoots(): array
    {
        return ['Event.Dashboard.default'];
    }
}
