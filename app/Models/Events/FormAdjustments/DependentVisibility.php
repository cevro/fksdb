<?php

namespace FKSDB\Models\Events\FormAdjustments;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Forms\IControl;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class DependentVisibility extends PairwiseAdjustment {

    protected function processPair(BaseControl $target, IControl $prerequisite): void {
        $target->getRules()->addConditionOn($prerequisite, Form::FILLED)->toggle($target->getHtmlId() . '-pair');
    }
}
