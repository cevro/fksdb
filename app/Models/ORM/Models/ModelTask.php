<?php

namespace FKSDB\Models\ORM\Models;

use FKSDB\Models\ORM\DbNames;
use FKSDB\Models\Utils\Utils;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Strings;
use Fykosak\NetteORM\AbstractModel;

/**
 * @property-read int series
 * @property-read string label
 * @property-read string name_cs
 * @property-read int task_id
 * @property-read int points
 * @property-read int year
 * @property-read int contest_id
 * @property-read ActiveRow contest
 * @property-read \DateTimeInterface submit_deadline
 * @property-read \DateTimeInterface submit_start
 */
class ModelTask extends AbstractModel {

    public function getFQName(): string {
        return sprintf('%s.%s %s', Utils::toRoman($this->series), $this->label, $this->name_cs);
    }

    /**
     * @param string|null $type ModelTaskContribution::TYPE_*
     * @return ModelTaskContribution[] indexed by contribution_id
     */
    public function getContributions(?string $type = null): array {
        $contributions = $this->related(DbNames::TAB_TASK_CONTRIBUTION, 'task_id');
        if ($type !== null) {
            $contributions->where(['type' => $type]);
        }

        $result = [];
        foreach ($contributions as $contribution) {
            $contributionModel = ModelTaskContribution::createFromActiveRow($contribution);
            $result[$contributionModel->contribution_id] = $contributionModel;
        }
        return $result;
    }

    /**
     * @return ModelTaskStudyYear[] indexed by study_year
     */
    public function getStudyYears(): array {
        $studyYears = $this->related(DbNames::TAB_TASK_STUDY_YEAR, 'task_id');

        $result = [];
        foreach ($studyYears as $studyYear) {
            $studyYearModel = ModelTaskStudyYear::createFromActiveRow($studyYear);
            $result[$studyYearModel->study_year] = $studyYearModel;
        }
        return $result;
    }

    public function webalizeLabel(): string {
        return Strings::webalize($this->label, null, false);
    }

    public function getContest(): ModelContest {
        return ModelContest::createFromActiveRow($this->contest);
    }
}
