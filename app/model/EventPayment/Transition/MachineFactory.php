<?php


namespace FKSDB\EventPayment\Transition;


use FKSDB\EventPayment\SymbolGenerator\SymbolGeneratorFactory;
use FKSDB\EventPayment\Transition\Transitions\Fyziklani13Payment;
use FKSDB\ORM\ModelEvent;
use Nette\NotImplementedException;

class MachineFactory {
    /**
     * @var TransitionsFactory
     */
    private $transitionsFactory;
    /**
     * @var SymbolGeneratorFactory
     */
    private $symbolGeneratorFactory;

    public function __construct(TransitionsFactory $transitionsFactory, SymbolGeneratorFactory $symbolGeneratorFactory) {
        $this->transitionsFactory = $transitionsFactory;
        $this->symbolGeneratorFactory = $symbolGeneratorFactory;
    }

    /**
     * @param ModelEvent $event
     * @return Machine
     */
    public function setUpMachine(ModelEvent $event): Machine {
        $factory = $this->createTransitionsGenerator($event);
        $machine = $factory->createMachine($event);
        $factory->createTransitions($machine);

        return $machine;
    }

    /**
     * @param ModelEvent $event
     * @return AbstractTransitionsGenerator
     * @throws NotImplementedException
     */
    private function createTransitionsGenerator(ModelEvent $event): AbstractTransitionsGenerator {
        if (($event->event_type_id === 1) && ($event->event_year === 13)) {
            return new Fyziklani13Payment($this->transitionsFactory, $this->symbolGeneratorFactory);
        }
        throw new NotImplementedException('Not implemented');
    }
}
