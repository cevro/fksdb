<?php

namespace FKSDB\Models\ORM\Models;

use FKSDB\Models\ORM\DbNames;
use Fykosak\NetteORM\AbstractModel;

/**
 *
 * @author Miroslav Jarý <mira.jary@gmail.com>
 * @property-read int submit_question_id
 * @property-read int ct_id
 * @property-read int question_id
 * @property-read \DateTimeInterface submitted_on
 * @property-read string answer
 */
class ModelSubmitQuizQuestion extends AbstractModel {

    public function getTask(): ModelTask {
        return ModelTask::createFromActiveRow($this->ref(DbNames::TAB_TASK, 'task_id'));
    }

    public function getContestant(): ModelContestant {
        return ModelContestant::createFromActiveRow($this->ref(DbNames::TAB_CONTESTANT_BASE, 'ct_id'));
    }
}
