<?php

declare(strict_types=1);

namespace FKSDB\Models\ORM\Links;

use FKSDB\Models\ORM\Models\ModelEvent;
use Fykosak\NetteORM\AbstractModel;

class ParticipantListLink extends LinkFactory
{

    public function getText(): string
    {
        return _('List of applications');
    }

    /**
     * @param AbstractModel|ModelEvent $model
     * @return string
     */
    protected function getDestination(AbstractModel $model): string
    {
        if ($model->isTeamEvent()) {
            return ':Event:TeamApplication:list';
        } else {
            return ':Event:Application:list';
        }
    }

    /**
     * @param AbstractModel|ModelEvent $model
     * @return array
     */
    protected function prepareParams(AbstractModel $model): array
    {
        return [
            'eventId' => $model->event_id,
        ];
    }
}
