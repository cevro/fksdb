<?php

namespace FKSDB\Components\Events;

use Events\Machine\BaseMachine;
use Events\Machine\Machine;
use Events\Model\Holder;
use Events\SubmitProcessingException;
use Events\TransitionConditionFailedException;
use Events\TransitionOnExecutedException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 * 
 * @author Michal Koutný <michal@fykos.cz>
 */
class ApplicationComponent extends Control {

    /**
     * @var Machine
     */
    private $machine;

    /**
     * @var Holder
     */
    private $holder;

    function __construct(Machine $machine, Holder $holder) {
        parent::__construct();
        $this->machine = $machine;
        $this->holder = $holder;
    }

    public function renderForm() {
        $this->getComponent('form')->render();
    }

    protected function createComponentForm($name) {
        $this->initializeMachine();
        $form = new Form();

        /*
         * Create containers
         */
        foreach ($this->holder as $name => $baseHolder) {
            $baseMachine = $this->machine[$name];
            if (!$baseHolder->isVisible($baseMachine)) {
                continue;
            }
            $container = $baseHolder->createFormContainer($baseMachine);
            $form->addComponent($container, $name);
        }

        /*
         * Create transition buttons
         */
        $primaryMachine = $this->machine->getPrimaryMachine();
        $that = $this;
        foreach ($primaryMachine->getAvailableTransitions() as $transition) {
            $transitionName = $transition->getName();
            $submit = $form->addSubmit($transitionName, $transition->getLabel());

            $submit->onClick[] = function(Form $form) use($transitionName, $that) {
                        $that->handleSubmit($form, $transitionName);
                    };
        }

        /*
         * Create save (no transition) button
         */
        //TODO display this button in dependence on modifiable
        if ($primaryMachine->getState() != BaseMachine::STATE_INIT) {
            $submit = $form->addSubmit('save', _('Uložit'));
            $submit->onClick[] = array($this, 'handleSubmit');
        }

        return $form;
    }

    private function handleSubmit(Form $form, $explicitTransitionName = null, $explicitMachineName = null) {
        $this->initializeMachine();
        $connection = $this->holder->getConnection();
        try {
            $values = $form->getValues();
            $explicitMachine = $explicitMachineName ? $this->machine->getPrimaryMachine() : $this->machine[$explicitMachineName];

            $connection->beginTransaction();

            /*
             * Find out transitions
             */
            $newStates = $this->holder->processValues($values);
            $transitions = array();
            foreach ($newStates as $name => $newState) {
                $transitions[$name] = $this->machine[$name]->getTransitionByTarget($newState);
            }

            if ($explicitTransitionName !== null) {
                if (isset($transitions[$explicitMachineName])) {
                    throw new MachineExectionException(sprintf('Collision of explicit transision %s and processing transition %s', $explicitTransitionName, $explicitTransitionName[$explicitMachineName]->getName()));
                }
                $transitions[$explicitMachineName] = $explicitMachine->getTransition($explicitTransitionName);
            }

            foreach ($transitions as $transition) {
                try {
                    $transition->execute();
                } catch (TransitionOnExecutedException $e) {
                    $form->addError($e->getMessage()); //TODO rather flash message due to only state change
                }
            }

            $this->holder->saveModels();
            $connection->commit();
            $this->redirect('this');
        } catch (TransitionConditionFailedException $e) {
            $form->addError($e->getMessage());
            $connection->rollBack();
        } catch (SubmitProcessingException $e) {
            $form->addError($e->getMessage());
            $connection->rollBack();
        }
    }

    private function initializeMachine() {
        $this->machine->setHolder($this->holder);
    }

}
