<?php

namespace FKSDB\ORM\Services\Events;

use AbstractServiceSingle;
use DbNames;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class ServiceDsefGroup extends AbstractServiceSingle {

    protected $tableName = DbNames::TAB_E_DSEF_GROUP;
    protected $modelClassName = 'FKSDB\ORM\Models\Events\ModelDsefGroup';

}

