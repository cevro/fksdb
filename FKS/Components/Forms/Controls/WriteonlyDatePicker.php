<?php

namespace FKS\Components\Forms\Controls;

use FKS\Components\Forms\Containers\IWriteonly;
use JanTvrdik\Components\DatePicker;

/**
 * When user doesn't fill it (i.e. desires original value), it behaves like disabled.
 * Only FILLED validation works properly because there's used special value to distinguish unchanged input.
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class WriteonlyDatePicker extends DatePicker implements IWriteonly {

    use WriteonlyTrait;

    public function __construct($label = NULL, $cols = NULL, $maxLength = NULL) {
        parent::__construct($label, $cols, $maxLength);
        $this->writeonlyAppendMonitors();
    }

    public function getControl() {
        $control = parent::getControl();
        $control = $this->writeonlyAdjustControl($control);
        return $control;
    }

    public function setValue($value) {
        if ($value == self::VALUE_ORIGINAL) {
            $this->value = $value;
            $this->rawValue = $value;
        } else {
            parent::setValue($value);
        }
    }

    public function loadHttpData() {
        parent::loadHttpData();
        $this->writeonlyLoadHttpData();
    }

    protected function attached($obj) {
        parent::attached($obj);
        $this->writeonlyAttached($obj);
    }

}
