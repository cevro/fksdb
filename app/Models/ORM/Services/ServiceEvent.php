<?php

namespace FKSDB\Models\ORM\Services;

use FKSDB\Models\ORM\DbNames;
use FKSDB\Models\ORM\Models\AbstractModelSingle;
use FKSDB\Models\ORM\Models\ModelContest;
use FKSDB\Models\ORM\Models\ModelEvent;
use FKSDB\Models\ORM\Models\ModelEventType;
use FKSDB\Models\ORM\Tables\TypedTableSelection;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 * @method ModelEvent createNewModel(array $data)
 * @method ModelEvent|null findByPrimary($key)
 * @method ModelEvent refresh(AbstractModelSingle $model)
 */
class ServiceEvent extends AbstractServiceSingle {

    public function getEvents(ModelContest $contest, int $year): TypedTableSelection {
        return $this->getTable()
            ->where(DbNames::TAB_EVENT_TYPE . '.contest_id', $contest->contest_id)
            ->where(DbNames::TAB_EVENT . '.year', $year);
    }

    public function getByEventTypeId(ModelContest $contest, int $year, int $eventTypeId): ?ModelEvent {
        /** @var ModelEvent $event */
        $event = $this->getEvents($contest, $year)->where(DbNames::TAB_EVENT . '.event_type_id', $eventTypeId)->fetch();
        return $event;
    }

    public function getEventsByType(ModelEventType $eventType): TypedTableSelection {
        return $this->getTable()->where('event_type_id', $eventType->event_type_id);
    }

    public function store(?ModelEvent $model, array $data): ModelEvent {
        if (is_null($model)) {
            return $this->createNewModel($data);
        } else {
            $this->updateModel2($model, $data);
            return $this->refresh($model);
        }
    }
}
