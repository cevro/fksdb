<?php

namespace FKSDB\ORM\Services;

use FKSDB\ORM\AbstractServiceSingle;
use FKSDB\ORM\DbNames;
use FKSDB\ORM\DeprecatedLazyDBTrait;
use FKSDB\ORM\Models\ModelPostContact;
use Nette\Database\Context;
use Nette\Database\IConventions;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class ServicePostContact extends AbstractServiceSingle {
    use DeprecatedLazyDBTrait;

    /**
     * ServicePostContact constructor.
     * @param Context $connection
     * @param IConventions $conventions
     */
    public function __construct(Context $connection, IConventions $conventions) {
        parent::__construct($connection, $conventions, DbNames::TAB_POST_CONTACT, ModelPostContact::class);
    }
}
