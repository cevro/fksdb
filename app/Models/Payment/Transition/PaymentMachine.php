<?php


namespace FKSDB\Models\Payment\Transition;

use FKSDB\Models\ORM\Models\AbstractModelSingle;
use FKSDB\Models\ORM\Models\ModelEvent;
use FKSDB\Models\ORM\Models\ModelPayment;
use FKSDB\Models\ORM\Services\ServiceEvent;
use FKSDB\Models\ORM\Services\ServicePayment;
use FKSDB\Models\Payment\PriceCalculator\PriceCalculator;
use FKSDB\Models\Transitions\TransitionsDecorator;
use FKSDB\Models\Transitions\Machine\Machine;
use Nette\Database\Explorer;

/**
 * Class PaymentMachine
 * @author Michal Červeňák <miso@fykos.cz>
 */
class PaymentMachine extends Machine {

    private PriceCalculator $priceCalculator;
    private ModelEvent $event;
    private ServiceEvent $serviceEvent;
    private array $scheduleGroupTypes;
    private ServicePayment $servicePayment;

    public function __construct(Explorer $explorer, ServicePayment $servicePayment, ServiceEvent $serviceEvent) {
        parent::__construct($explorer, $servicePayment);
        $this->serviceEvent = $serviceEvent;
        $this->servicePayment = $servicePayment;
    }

    public function decorateTransitions(TransitionsDecorator $decorator): void {
        $decorator->decorate($this);
    }

    public function setEventId(int $eventId): void {
        $event = $this->serviceEvent->findByPrimary($eventId);
        if (!is_null($event)) {
            $this->event = $event;
        }
    }

    public function setScheduleGroupTypes(array $types): void {
        $this->scheduleGroupTypes = $types;
    }

    public function getScheduleGroupTypes(): array {
        return $this->scheduleGroupTypes;
    }

    public function setPriceCalculator(PriceCalculator $priceCalculator): void {
        $this->priceCalculator = $priceCalculator;
    }

    public function getPriceCalculator(): PriceCalculator {
        return $this->priceCalculator;
    }

    public function getEvent(): ModelEvent {
        return $this->event;
    }

    public function getCreatingState(): string {
        return ModelPayment::STATE_NEW;
    }

    public function createHolder(AbstractModelSingle $model): PaymentHolder {
        return new PaymentHolder($model, $this->servicePayment);
    }
}
