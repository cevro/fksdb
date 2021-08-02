<?php

declare(strict_types=1);

namespace FKSDB\Models\Events\FormAdjustments;

use Nette\Forms\Control;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;

class DependentRequirements extends PairwiseAdjustment
{

    protected function processPair(BaseControl $target, Control $prerequisite): void
    {
        $target->getRules()->addConditionOn($prerequisite, Form::FILLED)->addRule(
            Form::FILLED,
            _('Field %label is required.')
        );
    }
}
