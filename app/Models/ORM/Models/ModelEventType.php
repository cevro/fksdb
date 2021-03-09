<?php

namespace FKSDB\Models\ORM\Models;

use Nette\Database\Table\ActiveRow;
use Fykosak\NetteORM\AbstractModel;

/**
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 * @property-read ActiveRow contest
 * @property-read int contest_id
 * @property-read int event_type_id
 */
class ModelEventType extends AbstractModel {

    public const FYZIKLANI = 1;

    public function getContest(): ModelContest {
        return ModelContest::createFromActiveRow($this->contest);
    }
}
