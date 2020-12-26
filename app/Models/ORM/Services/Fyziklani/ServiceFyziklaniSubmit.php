<?php

namespace FKSDB\Models\ORM\Services\Fyziklani;

use FKSDB\Models\ORM\Models\Fyziklani\ModelFyziklaniSubmit;
use FKSDB\Models\ORM\Services\AbstractServiceSingle;

/**
 * @author Lukáš Timko <lukast@fykos.cz>
 * @method ModelFyziklaniSubmit createNewModel(array $data)
 */
class ServiceFyziklaniSubmit extends AbstractServiceSingle {

<<<<<<< HEAD
=======


>>>>>>> 5fb6a5848edeb949c089ce68e107402352d64d58
    public function findByTaskAndTeam(ModelFyziklaniTask $task, ModelFyziklaniTeam $team): ?ModelFyziklaniSubmit {
        /** @var ModelFyziklaniSubmit $row */
        $row = $this->getTable()->where([
            'fyziklani_task_id' => $task->fyziklani_task_id,
            'e_fyziklani_team_id' => $team->e_fyziklani_team_id,
        ])->fetch();
        return $row ?: null;
    }

    public function findAll(ModelEvent $event): TypedTableSelection {
        return $this->getTable()->where('e_fyziklani_team_id.event_id', $event->event_id);
    }

    public function getSubmitsAsArray(ModelEvent $event, ?string $lastUpdated): array {
        $query = $this->getTable()->where('e_fyziklani_team.event_id', $event->event_id);
        $submits = [];
        if ($lastUpdated) {
            $query->where('modified >= ?', $lastUpdated);
        }
        foreach ($query as $row) {
            $submit = ModelFyziklaniSubmit::createFromActiveRow($row);
            $submits[$submit->fyziklani_submit_id] = $submit->__toArray();
        }
        return $submits;
    }
}
