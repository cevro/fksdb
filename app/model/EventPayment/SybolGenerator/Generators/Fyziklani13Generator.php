<?php

namespace FKSDB\EventPayment\SymbolGenerator\Generators;

use FKSDB\EventPayment\SymbolGenerator\AlreadyGeneratedSymbols;
use FKSDB\ORM\ModelEventPayment;

class Fyziklani13Generator extends \FKSDB\EventPayment\SymbolGenerator\AbstractSymbolGenerator {
    public function __construct(\ServiceEventPayment $serviceEventPayment) {
        parent::__construct($serviceEventPayment);
    }

    public function create(ModelEventPayment $modelEventPayment) {

        if ($modelEventPayment->hasGeneratedSymbols()) {
            throw new AlreadyGeneratedSymbols(\sprintf(_('Payment #%s has already generated symbols.'), $modelEventPayment->getPaymentId()));
        }
        $maxVariableSymbol = $this->serviceEventPayment->where('event_id', $modelEventPayment->event_id)->max('variable_symbol');
        $variableId = $maxVariableSymbol % 7292000;
        $variableNumber = $variableId + 1 + 7292000;

        return [
            'constant_symbol' => 1234,
            'variable_symbol' => $variableNumber,
            'specific_symbol' => 1234,
            'bank_account' => '123456789/1234',
        ];
    }
}
