<?php

namespace FKSDB\Models\ValuePrinters;

use FKSDB\Models\Payment\Price;
use FKSDB\Models\Payment\PriceCalculator\UnsupportedCurrencyException;
use Nette\Utils\Html;

class PricePrinter extends AbstractValuePrinter {
    /**
     * @param Price $value
     * @throws UnsupportedCurrencyException
     */
    protected function getHtml($value): Html {
        return Html::el('span')->addText($value->__toString());
    }
}
