<?php

namespace FKSDB\Models\Events\FormAdjustments;

use FKSDB\Models\Events\Model\Holder\Holder;
use Nette\Forms\Form;
use Nette\Forms\Control;
use Nette\Utils\Strings;

/**
 * More user friendly Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class RegexpCheck extends AbstractAdjustment implements FormAdjustment {

    private string $field;
    private string $message;
    private string $pattern;

    public function __construct(string $field, string $message, string $pattern) {
        $this->field = $field;
        $this->message = $message;
        $this->pattern = $pattern;
    }

    protected function innerAdjust(Form $form, Holder $holder): void {
        $controls = $this->getControl($this->field);
        if (!$controls) {
            return;
        }
        foreach ($controls as $control) {
            $control->addRule(function (Control $control): bool {
                return (bool)Strings::match($control->getValue(), $this->pattern);
            }, $this->message);
        }
    }
}
