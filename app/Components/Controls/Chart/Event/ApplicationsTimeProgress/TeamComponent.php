<?php

namespace FKSDB\Components\Controls\Chart\Event\ApplicationsTimeProgress;

use FKSDB\Components\Controls\Chart\Contestants\Core\Chart;
use FKSDB\Components\React\ReactComponent;
use FKSDB\Model\ORM\Models\ModelEvent;
use FKSDB\Model\ORM\Models\ModelEventType;
use FKSDB\Model\ORM\Services\Fyziklani\ServiceFyziklaniTeam;
use FKSDB\Model\ORM\Services\ServiceEvent;
use Nette\DI\Container;

/**
 * Class TeamApplicationsTimeProgress
 * @author Michal Červeňák <miso@fykos.cz>
 */
class TeamComponent extends ReactComponent implements Chart {

    private ServiceFyziklaniTeam $serviceFyziklaniTeam;
    private ModelEventType $eventType;
    private ServiceEvent $serviceEvent;

    public function __construct(Container $context, ModelEvent $event) {
        parent::__construct($context, 'chart.events.teams.time-progress');
        $this->eventType = $event->getEventType();
    }

    final public function injectPrimary(ServiceFyziklaniTeam $serviceFyziklaniTeam, ServiceEvent $serviceEvent): void {
        $this->serviceFyziklaniTeam = $serviceFyziklaniTeam;
        $this->serviceEvent = $serviceEvent;
    }

    protected function getData(): array {
        $data = [
            'teams' => [],
            'events' => [],
        ];
        /** @var ModelEvent $event */
        foreach ($this->serviceEvent->getEventsByType($this->eventType) as $event) {
            $data['teams'][$event->event_id] = $this->serviceFyziklaniTeam->getTeamsAsArray($event);
            $data['events'][$event->event_id] = $event->__toArray();
        }
        return $data;
    }

    public function getTitle(): string {
        return _('Team applications time progress');
    }

    public function getControl(): self {
        return $this;
    }

    public function getDescription(): ?string {
        return null;
    }
}
