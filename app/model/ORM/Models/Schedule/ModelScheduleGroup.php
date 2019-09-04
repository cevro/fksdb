<?php

namespace FKSDB\ORM\Models\Schedule;

use DateTime;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\DbNames;
use FKSDB\ORM\Models\ModelEvent;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\GroupedSelection;

/**
 * Class ModelScheduleGroup
 * @package FKSDB\ORM\Models\Schedule
 * @property-read int schedule_group_id
 * @property-read string schedule_group_type
 * @property-read int event_id
 * @property-read ActiveRow event
 * @property-read DateTime start
 * @property-read DateTime end
 */
class ModelScheduleGroup extends AbstractModelSingle {
    const TYPE_ACCOMMODATION = 'accommodation';
    const TYPE_DSEF_GROUP = 'dsef-group';

    /**
     * @return GroupedSelection
     */
    public function getItems(): GroupedSelection {
        return $this->related(DbNames::TAB_SCHEDULE_ITEM);
    }

    /**
     * @return ModelEvent
     */
    public function getEvent(): ModelEvent {
        return ModelEvent::createFromActiveRow($this->event);
    }

    /**
     * @return array
     */
    public function __toArray(): array {
        return [
            'scheduleGroupId' => $this->schedule_group_id,
            'scheduleGroupType' => $this->schedule_group_type,
            'eventId' => $this->event_id,
            'start' => $this->start->format('c'),
            'end' => $this->end->format('c'),
        ];
    }
}
