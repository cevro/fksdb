<?php

namespace FKSDB\ORM\Services\Events;

use FKSDB\ORM\AbstractServiceSingle;
use FKSDB\ORM\DbNames;
use FKSDB\ORM\Models\Events\ModelVikendParticipant;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class ServiceVikendParticipant extends AbstractServiceSingle {

    public function getModelClassName(): string {
        return ModelVikendParticipant::class;
    }

    protected function getTableName(): string {
        return DbNames::TAB_E_VIKEND_PARTICIPANT;
    }
}
