<?php

namespace FKSDB\Components\Forms\Controls;

use FKSDB\Application\IJavaScriptCollector;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Form;
use Nette\Utils\Html;

/**
 * When user doesn't fill it (i.e. desires original value), it behaves like disabled.
 * Only FILLED validation works properly because there's used special value to distinguish unchanged input.
 *
 * @note When using this trait, be sure to call:
 *           writeOnlyAppendMonitors in __constuctor
 *           writeOnlyAdjustControl in getControl
 *           writeOnlyLoadHttpData in loadHttpData after original loadHttpData
 *       and writeOnlyAttached in attached.
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
trait WriteOnlyTrait {

    /**
     * @var bool
     */
    private $writeOnly = true;
    /**
     * @var bool
     */
    private $actuallyDisabled = false;
    /**
     * @var bool
     */
    private $hasManualValue = false;

    private function writeOnlyAppendMonitors() {
        $this->monitor(Form::class);
        $this->monitor(IJavaScriptCollector::class);
    }

    public function getWriteOnly(): bool {
        return $this->writeOnly;
    }

    /**
     * @param bool $writeOnly
     * @return void
     */
    public function setWriteOnly(bool $writeOnly = true) {
        $this->writeOnly = $writeOnly;
    }

    /**
     * @param Html $control
     * @return Html
     */
    private function writeOnlyAdjustControl(Html $control) {
// rendered control may not disabled
        $control->addAttributes([
            'disabled' => $this->actuallyDisabled,
        ]);

// don't show the value (only if it's form displayed after submit)
// for JS
        if ($this->writeOnly && $this->getValue() && !$this->hasManualValue) {
            $control->addAttributes([
                'data-writeOnly' => (int)true,
                'data-writeOnly-value' => self::VALUE_ORIGINAL,
                'data-writeOnly-label' => _('Hidden value'),
                'value' => self::VALUE_ORIGINAL,
            ]);
        }
        return $control;
    }

    protected function writeOnlyLoadHttpData() {
        if ($this->getValue() != self::VALUE_ORIGINAL) {
            $this->hasManualValue = true;
        }
    }

    private function writeOnlyDisable() {
        $this->actuallyDisabled = $this->isDisabled();
        $this->setDisabled();
    }

    /**
     * @var bool
     */
    private $writeOnlyAttachedOnValidate = false;
    /**
     * @var bool
     */
    private $writeOnlyAttachedJS = false;

    /**
     * @param IComponent $obj
     * @return void
     */
    protected function writeOnlyAttached($obj) {
        if (!$this->writeOnlyAttachedOnValidate && $obj instanceof Form) {
            $that = $this;
            $obj->onValidate = $obj->onValidate ?: [];
            array_unshift($obj->onValidate, function (Form $form) use ($that) {
                if ($that->writeOnly && $that->getValue() == self::VALUE_ORIGINAL) {
                    $that->writeOnlyDisable();
                }
            });
            $this->writeOnlyAttachedOnValidate = true;
        }
        if (!$this->writeOnlyAttachedJS && $obj instanceof IJavaScriptCollector) {
            $this->writeOnlyAttachedJS = true;
            $obj->registerJSFile('js/writeOnlyInput.js');
        }
    }

}
