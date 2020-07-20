<?php

namespace FKSDB\Components\Forms\Controls\Payment;

use FKSDB\Components\React\ReactComponentTrait;
use FKSDB\Exceptions\NotImplementedException;
use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Models\Schedule\ModelPersonSchedule;
use FKSDB\ORM\Services\Schedule\ServicePersonSchedule;
use Nette\Application\BadRequestException;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\JsonException;

/**
 * Class PaymentSelectField
 * @author Michal Červeňák <miso@fykos.cz>
 */
class PaymentSelectField extends TextInput {

    use ReactComponentTrait;

    /** @var ServicePersonSchedule */
    private $servicePersonSchedule;
    /** @var ModelEvent */
    private $event;
    /** @var string */
    private $groupTypes;
    /** @var bool */
    private $showAll;

    /**
     * PaymentSelectField constructor.
     * @param ServicePersonSchedule $servicePersonSchedule
     * @param ModelEvent $event
     * @param array $groupTypes
     * @param bool $showAll
     * @throws BadRequestException
     * @throws JsonException
     */
    public function __construct(ServicePersonSchedule $servicePersonSchedule, ModelEvent $event, array $groupTypes, bool $showAll = true) {
        parent::__construct();
        $this->servicePersonSchedule = $servicePersonSchedule;
        $this->event = $event;
        $this->groupTypes = $groupTypes;
        $this->showAll = $showAll;
        $this->registerReact('payment.schedule-select');
        $this->appendProperty();
    }

    /**
     * @param mixed ...$args
     * @return string
     * @throws NotImplementedException
     */
    public function getData(...$args): string {
        $query = $this->servicePersonSchedule->getTable()->where('schedule_item.schedule_group.event_id', $this->event->event_id);
        if (count($this->groupTypes)) {
            $query->where('schedule_item.schedule_group.schedule_group_type IN', $this->groupTypes);
        }
        $items = [];
        /** @var ModelPersonSchedule $model */
        foreach ($query as $model) {
            $model->getPayment();
            if ($this->showAll || !$model->hasActivePayment()) {
                $items[] = [
                    'hasPayment' => false, //$model->hasActivePayment(),
                    'label' => $model->getLabel(),
                    'id' => $model->person_schedule_id,
                    'scheduleItem' => $model->getScheduleItem()->__toArray(),
                    'personId' => $model->person_id,
                    'personName' => $model->getPerson()->getFullName(),
                    'personFamilyName' => $model->getPerson()->family_name,
                ];
            }
        }
        return \json_encode($items);
    }
}
