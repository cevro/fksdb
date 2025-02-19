<?php

namespace FKSDB\Models\Events\Machine;

use FKSDB\Models\Events\Exceptions\TransitionConditionFailedException;
use FKSDB\Models\Events\Exceptions\TransitionOnExecutedException;
use FKSDB\Models\Events\Exceptions\TransitionUnsatisfiedTargetException;
use FKSDB\Models\Events\Model\Holder\BaseHolder;
use FKSDB\Models\Events\Model\Holder\Holder;
use Nette\InvalidArgumentException;

class Transition extends \FKSDB\Models\Transitions\Transition\Transition {

    private BaseMachine $baseMachine;
    private array $inducedTransitions = [];
    private string $mask;
    private string $name;
    private string $source;
    /** @var bool|callable */
    private $visible;
    public array $onExecuted = [];

    public function __construct(string $mask, ?string $label = null, string $type = self::TYPE_DEFAULT) {
        $this->setMask($mask);
        $this->setLabel($label ?? '');
        $this->setBehaviorType($type);
    }

    /**
     * Meaningless identifier.
     */
    public function getName(): string {
        return $this->name;
    }

    public function getBehaviorType(): string {
        if ($this->isTerminating()) {
            return self::TYPE_DANGEROUS;
        }
        if ($this->isCreating()) {
            return self::TYPE_SUCCESS;
        }
        return parent::getBehaviorType();
    }

    private function setName(string $mask): void {
        // it's used for component naming
        $name = str_replace('*', '_any_', $mask);
        $name = str_replace('|', '_or_', $name);
        $this->name = preg_replace('/[^a-z0-9_]/i', '_', $name);
    }

    public function getMask(): string {
        return $this->mask;
    }

    public function setMask(string $mask): void {
        $this->mask = $mask;
        [$this->source, $target] = self::parseMask($mask);
        $this->setTargetState($target);
        $this->setName($mask);
    }

    public function getBaseMachine(): BaseMachine {
        return $this->baseMachine;
    }

    public function setBaseMachine(BaseMachine $baseMachine): void {
        $this->baseMachine = $baseMachine;
    }

    public function getSource(): string {
        return $this->source;
    }

    public function isCreating(): bool {
        return strpos($this->source, \FKSDB\Models\Transitions\Machine\Machine::STATE_INIT) !== false;
    }

    public function isVisible(Holder $holder): bool {
        return $this->getEvaluator()->evaluate($this->visible, $holder);
    }

    /**
     * @param callable|bool $visible
     */
    public function setVisible($visible): void {
        $this->visible = $visible;
    }

    public function addInducedTransition(BaseMachine $targetMachine, string $targetState): void {
        if ($targetMachine === $this->getBaseMachine()) {
            throw new InvalidArgumentException('Cannot induce transition in the same machine.');
        }
        $targetName = $targetMachine->getName();
        if (isset($this->inducedTransitions[$targetName])) {
            throw new InvalidArgumentException("Induced transition for machine $targetName already defined in " . $this->getName() . '.');
        }
        $this->inducedTransitions[$targetName] = $targetState;
    }

    /**
     * @return Transition[]
     */
    private function getInducedTransitions(Holder $holder): array {
        $result = [];
        foreach ($this->inducedTransitions as $baseMachineName => $targetState) {
            $targetMachine = $this->getBaseMachine()->getMachine()->getBaseMachine($baseMachineName);
            $oldState = $holder->getBaseHolder($baseMachineName)->getModelState();
            $inducedTransition = $targetMachine->getTransitionByTarget($oldState, $targetState);
            if ($inducedTransition) {
                $result[$baseMachineName] = $inducedTransition;
            }
        }
        return $result;
    }

    private function getBlockingTransition(Holder $holder): ?Transition {
        foreach ($this->getInducedTransitions($holder) as $inducedTransition) {
            if ($inducedTransition->getBlockingTransition($holder)) {
                return $inducedTransition;
            }
        }
        if (!$this->isConditionFulfilled($holder)) {
            return $this;
        }
        return null;
    }

    /**
     * @param Transition[] $inducedTransitions
     */
    private function validateTarget(Holder $holder, array $inducedTransitions): ?array {
        foreach ($inducedTransitions as $inducedTransition) {
            $result = $inducedTransition->validateTarget($holder, []);
            if (!is_null($result)) {
                return $result;
            }
        }

        $baseHolder = $holder->getBaseHolder($this->getBaseMachine()->getName());
        $validator = $baseHolder->getValidator();
        $validator->validate($baseHolder);
        return $validator->getValidationResult();
    }

    final public function canExecute(Holder $holder): bool {
        return !$this->getBlockingTransition($holder);
    }

    /**
     * @return bool|callable
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * Launch induced transitions and sets new state.
     * @throws TransitionConditionFailedException
     * @throws TransitionUnsatisfiedTargetException
     * @todo Induction work only for one level.
     */
    final public function execute(Holder $holder): array {
        $blockingTransition = $this->getBlockingTransition($holder);
        if ($blockingTransition) {
            throw new TransitionConditionFailedException($blockingTransition);
        }

        $inducedTransitions = [];
        foreach ($this->getInducedTransitions($holder) as $holderName => $inducedTransition) {
            $inducedTransition->changeState($holder->getBaseHolder($holderName));
            $inducedTransitions[] = $inducedTransition;
        }

        $this->changeState($holder->getBaseHolder($this->getBaseMachine()->getName()));

        $validationResult = $this->validateTarget($holder, $inducedTransitions);
        if (!is_null($validationResult)) {
            throw new TransitionUnsatisfiedTargetException($validationResult);
        }

        return $inducedTransitions;
    }

    /**
     * Triggers onExecuted event.
     *
     * @param Transition[] $inducedTransitions
     * @throws TransitionOnExecutedException
     */
    final public function executed(Holder $holder, array $inducedTransitions): void {
        foreach ($inducedTransitions as $inducedTransition) {
            $inducedTransition->executed($holder, []);
        }
        try {
            $this->callAfterExecute($this, $holder);
        } catch (\Exception $exception) {
            throw new TransitionOnExecutedException($this->getName(), null, $exception);
        }
    }

    /**
     * @note Assumes the condition is fulfilled.
     */
    private function changeState(BaseHolder $holder): void {
        $holder->setModelState($this->getTargetState());
    }

    /**
     * @param string $mask It may be either mask of initial state or mask of whole transition.
     */
    public function matches(string $mask): bool {
        $parts = self::parseMask($mask);

        if (count($parts) == 2 && $parts[1] != $this->getTargetState()) {
            return false;
        }
        $stateMask = $parts[0];

        /*
         * Star matches any state but meta-states (initial and terminal)
         */
        if (strpos(\FKSDB\Models\Transitions\Machine\Machine::STATE_ANY, $stateMask) !== false || (strpos(\FKSDB\Models\Transitions\Machine\Machine::STATE_ANY, $this->source) !== false &&
                ($mask != \FKSDB\Models\Transitions\Machine\Machine::STATE_INIT && $mask != \FKSDB\Models\Transitions\Machine\Machine::STATE_TERMINATED))) {
            return true;
        }

        return preg_match("/(^|\\|){$stateMask}(\\||\$)/", $this->source);
    }

    /**
     * @note Assumes mask is valid.
     */
    private static function parseMask(string $mask): array {
        return explode('->', $mask);
    }

    public static function validateTransition(string $mask, array $states): bool {
        $parts = self::parseMask($mask);
        if (count($parts) != 2) {
            return false;
        }
        [$sources, $target] = $parts;

        $sources = explode('|', $sources);

        foreach ($sources as $source) {
            if (!in_array($source, array_merge($states, [\FKSDB\Models\Transitions\Machine\Machine::STATE_ANY, \FKSDB\Models\Transitions\Machine\Machine::STATE_INIT]))) {
                return false;
            }
        }
        if (!in_array($target, array_merge($states, [\FKSDB\Models\Transitions\Machine\Machine::STATE_TERMINATED]))) {
            return false;
        }
        return true;
    }
}
