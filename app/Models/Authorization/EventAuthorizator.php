<?php

namespace FKSDB\Models\Authorization;

use FKSDB\Models\ORM\Models\ModelEvent;
use FKSDB\Models\ORM\Models\ModelLogin;
use Nette\Security\Resource;
use Nette\Security\IUserStorage;
use Nette\Security\Permission;
use Nette\SmartObject;

class EventAuthorizator {

    use SmartObject;

    private IUserStorage $userStorage;
    private Permission $permission;
    private ContestAuthorizator $contestAuthorizator;

    public function __construct(IUserStorage $identity, Permission $acl, ContestAuthorizator $contestAuthorizator) {
        $this->contestAuthorizator = $contestAuthorizator;
        $this->userStorage = $identity;
        $this->permission = $acl;
    }

    /**
     * @param Resource|string|null $resource
     * @param string|null $privilege
     * @param ModelEvent $event
     * @return bool
     * @deprecated
     */
    public function isAllowed($resource, ?string $privilege, ModelEvent $event): bool {
        return $this->contestAuthorizator->isAllowed($resource, $privilege, $event->getContest());
    }

    /**
     * @param Resource|string $resource
     * @param string|null $privilege
     * @param ModelEvent $event
     * @return bool
     */
    public function isContestOrgAllowed($resource, ?string $privilege, ModelEvent $event): bool {
        return $this->contestAuthorizator->isAllowed($resource, $privilege, $event->getContest());
    }

    /**
     * @param Resource|string|null $resource
     * @param string|null $privilege
     * @param ModelEvent $event
     * @return bool
     */
    public function isEventOrContestOrgAllowed($resource, ?string $privilege, ModelEvent $event): bool {
        if (!$this->userStorage->isAuthenticated()) {
            return false;
        }
        if ($this->isContestOrgAllowed($resource, $privilege, $event)) {
            return true;
        }
        return $this->isEventOrg($resource, $privilege, $event);
    }

    /**
     * @param Resource|string|null $resource
     * @param string|null $privilege
     * @param ModelEvent $event
     * @return bool
     */
    public function isEventAndContestOrgAllowed($resource, ?string $privilege, ModelEvent $event): bool {
        if (!$this->userStorage->isAuthenticated()) {
            return false;
        }
        if (!$this->isEventOrg($resource, $privilege, $event)) {
            return false;
        }
        return $this->contestAuthorizator->isAllowed($resource, $privilege, $event->getContest());
    }

    /**
     * @param Resource|string $resource
     * @param string|null $privilege
     * @param ModelEvent $event
     * @return bool
     */
    private function isEventOrg($resource, ?string $privilege, ModelEvent $event): bool {
        /** @var ModelLogin $identity */
        $identity = $this->userStorage->getIdentity();
        $person = $identity ? $identity->getPerson() : null;
        if (!$person) {
            return false;
        }
        return $event->getEventOrgs()->where('person_id', $person->person_id)->count() > 0;
    }
}
