<?php

namespace FKSDB\Components\Forms;

use LogicException;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Controls\HiddenField;

/**
 * Form that uses optimistic locking to control multiple user access.
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class OptimisticForm extends Form {

    private const FINGERPRINT = '__fp';

    /** @var callable */
    private $fingerprintCallback;

    /** @var callable */
    private $defaultsCallback;

    /**
     *
     * @param callable $fingerprintCallback returns fingerprint of current version of the data
     * @param callable $defaultsCallback returns current version of data, formatted as an array
     */
    public function __construct(callable $fingerprintCallback, callable $defaultsCallback) {
        parent::__construct();
        $this->fingerprintCallback = $fingerprintCallback;
        $this->defaultsCallback = $defaultsCallback;
        $this->addHidden(self::FINGERPRINT);
    }

    /**
     * @param null $values Must be always null! Defaults callback is used to produce the values.
     * @param bool $erase
     * @throws LogicException
     */
    public function setDefaults($values = null, $erase = false) {
        if ($values !== null) {
            throw new LogicException('Default values in ' . __CLASS__ . ' are set by the callback.');
        }

        $defaults = ($this->defaultsCallback)();

        parent::setDefaults($defaults, $erase);

        if (!$this->isAnchored() || !$this->isSubmitted()) {
            $this->setFingerprint(($this->fingerprintCallback)());
        }
    }

    /**
     * @return HiddenField|IComponent
     */
    private function getFingerprintInput(): HiddenField {
        return $this[self::FINGERPRINT];
    }

    public function isValid(): bool {
        $receivedFingerprint = $this->getFingerprintInput()->getValue();
        $currentFingerprint = ($this->fingerprintCallback)();

        if ($receivedFingerprint != $currentFingerprint) {
            $this->addError(_('Od zobrazení formuláře byla změněna jeho data.'));
            $this->setFingerprint($currentFingerprint);
            parent::setValues(($this->defaultsCallback)());
            return false;
        }
        return parent::isValid();
    }

    private function setFingerprint(string $fingerprint): void {
        $this->getFingerprintInput()->setValue($fingerprint);
    }
}
