<?php

namespace FKSDB\DataTesting\Tests\Person;

use FKSDB\Logging\ILogger;
use FKSDB\ORM\Models\ModelContest;
use FKSDB\ORM\Models\ModelEventParticipant;
use FKSDB\ORM\Models\ModelPerson;
use FKSDB\DataTesting\TestLog;

/**
 * Class ParticipantsDurationTest
 * @author Michal Červeňák <miso@fykos.cz>
 */
class ParticipantsDurationTest extends PersonTest {

    /**
     * @param ILogger $logger
     * @param ModelPerson $person
     * @return void
     */
    public function run(ILogger $logger, ModelPerson $person) {
        $contestsDefs = [
            ModelContest::ID_FYKOS => ['thresholds' => [4, 6]],
            ModelContest::ID_VYFUK => ['thresholds' => [4, 6]]
        ];

        foreach ($contestsDefs as $contestId => $contestsDef) {
            $max = null;
            $min = null;
            foreach ($person->getEventParticipant() as $row) {
                $model = ModelEventParticipant::createFromActiveRow($row);
                $event = $model->getEvent();
                if ($event->getEventType()->contest_id !== $contestId) {
                    continue;
                }
                $year = $event->year;

                $max = (is_null($max) || $max < $year) ? $year : $max;
                $min = (is_null($min) || $min > $year) ? $year : $min;
            }

            $delta = ($max - $min) + 1;
            $logger->log(new TestLog(
                $this->getTitle(),
                \sprintf('Person participate %d years in the events of contestId %d', $delta, $contestId),
                $this->evaluateThresholds($delta, $contestsDef['thresholds'])
            ));
        }

    }

    final private function evaluateThresholds(int $delta, array $thresholds): string {
        if ($delta < $thresholds[0]) {
            return TestLog::LVL_SUCCESS;
        }
        if ($delta < $thresholds[1]) {
            return TestLog::LVL_WARNING;
        }
        return TestLog::LVL_DANGER;
    }

    public function getAction(): string {
        return 'participants_duration';
    }

    public function getTitle(): string {
        return _('Participate events');
    }
}
