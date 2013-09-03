<?php

namespace Authorization;

use ModelContest;
use Nette\Security\Permission;
use Nette\Security\User;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class ContestAuthorizator {

    /**
     * @var User
     */
    private $user;

    /**
     * @var Permission
     */
    private $acl;

    function __construct(User $identity, Permission $acl) {
        $this->user = $identity;
        $this->acl = $acl;
    }

    public function getUser() {
        return $this->user;
    }

    protected function getAcl() {
        return $this->acl;
    }

    public function isAllowed($resource, $privilege, ModelContest $contest) {
        if (!$this->getUser()->isLoggedIn()) {
            return false;
        }

        $roles = $this->getUser()->getIdentity()->getRoles();

        foreach ($roles as $role) {
            if ($role->getContestId() != $contest->contest_id) {
                continue;
            }
            if ($this->acl->isAllowed($role->getRoleId(), $resource, $privilege)) {
                return true;
            }
        }
        
        return false;
    }

}
