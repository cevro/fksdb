<?php

namespace FKSDB\Authorization\Assertions;

use Nette\Database\Context;
use Nette\Security\IUserStorage;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 * @deprecated
 */
class EventOrgByYearAssertion extends AbstractEventOrgAssertion {

    public function __construct(IUserStorage $user, Context $connection) {
        parent::__construct('year', $user, $connection);
    }
}
