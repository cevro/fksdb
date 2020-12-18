<?php

namespace FKSDB\Model\Payment\SymbolGenerator\Generators;

use FKSDB\Model\Transitions\Holder\IModelHolder;
use FKSDB\Model\Transitions\Transition\Callbacks\ITransitionCallback;
use FKSDB\Model\ORM\Models\ModelPayment;
use FKSDB\Model\ORM\Services\ServicePayment;
use FKSDB\Model\Payment\PriceCalculator\UnsupportedCurrencyException;
use FKSDB\Model\Payment\SymbolGenerator\AlreadyGeneratedSymbolsException;

/**
 * Class AbstractSymbolGenerator
 * @author Michal Červeňák <miso@fykos.cz>
 */
abstract class AbstractSymbolGenerator implements ITransitionCallback {

    protected ServicePayment $servicePayment;

    public function __construct(ServicePayment $servicePayment) {
        $this->servicePayment = $servicePayment;
    }

    /**
     * @param ModelPayment $modelPayment
     * @param $args
     * @return array
     * @throws AlreadyGeneratedSymbolsException
     * @throws UnsupportedCurrencyException
     */
    abstract protected function create(ModelPayment $modelPayment, ...$args): array;

    /**
     * @param IModelHolder $model
     * @param $args
     * @throws AlreadyGeneratedSymbolsException
     * @throws UnsupportedCurrencyException
     */
    final public function __invoke(IModelHolder $model, ...$args): void {
        $info = $this->create($model, ...$args);
        $this->servicePayment->updateModel2($model, $info);
    }

    /**
     * @param IModelHolder $modelPayment
     * @param $args
     * @throws AlreadyGeneratedSymbolsException
     * @throws UnsupportedCurrencyException
     */
    final public function invoke(IModelHolder $model, ...$args): void {
        $info = $this->create($model, ...$args);
        $this->servicePayment->updateModel2($model, $info);
    }
}
