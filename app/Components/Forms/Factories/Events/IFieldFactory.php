<?php

namespace FKSDB\Components\Forms\Factories\Events;

use Events\Machine\BaseMachine;
use Events\Model\Field;
use Nette\ComponentModel\Component;
use Nette\Forms\Container;
use Nette\Forms\IControl;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
interface IFieldFactory {

    /**
     * @param Field $field          field for which it's created
     * @param BaseMachine $machine  appropiate base machine
     * @param Container $container  whole container of the base holder
     */
    public function create(Field $field, BaseMachine $machine, Container $container);

    /**
     * For its own output, it must be able to find the control that may be used
     * for form rules (dependecies).
     * 
     * @param Component $component
     * @return IControl
     */
    public function getMainControl(Component $component);
}
