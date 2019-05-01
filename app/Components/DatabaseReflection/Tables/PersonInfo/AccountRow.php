<?php

namespace FKSDB\Components\DatabaseReflection\PersonInfo;

use FKSDB\Components\DatabaseReflection\AbstractRow;
use FKSDB\Components\DatabaseReflection\DefaultPrinterTrait;
use FKSDB\Components\Forms\Controls\WriteOnlyInput;
use Nette\Forms\Form;
use Nette\Forms\IControl;

/**
 * Class AccountField
 * @package FKSDB\Components\Forms\Factories\PersonInfo
 */
class AccountRow extends AbstractRow {
    use DefaultPrinterTrait;

    /**
     * @return string
     */
    public function getTitle(): string {
        return _('Bank account');
    }

    /**
     * @return IControl
     */
    public function creteField(): IControl {
        $control = new WriteOnlyInput($this->getTitle());
        $control->addRule(Form::MAX_LENGTH, null, 32);
        return $control;
    }

    /**
     * @return int
     */
    public function getPermissionsValue(): int {
        return self::PERMISSION_ALLOW_FULL;
    }

    /**
     * @return string
     */
    protected function getModelAccessKey(): string {
        return 'account';
    }
}
