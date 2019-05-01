<?php

namespace FKSDB\Components\DatabaseReflection\PersonInfo;

use FKSDB\Components\DatabaseReflection\AbstractRow;
use FKSDB\Components\DatabaseReflection\DefaultPrinterTrait;
use FKSDB\Components\Forms\Controls\URLTextBox;
use Nette\Forms\Controls\BaseControl;

/**
 * Class HomepageField
 * @package FKSDB\Components\Forms\Factories\PersonInfo
 */
class HomepageRow extends AbstractRow {
    use DefaultPrinterTrait;

    /**
     * @return string
     */
    public function getTitle(): string {
        return _('Homepage');
    }

    /**
     * @return BaseControl
     */
    public function createField(): BaseControl {
        return new URLTextBox();
    }

    /**
     * @return int
     */
    public function getPermissionsValue(): int {
        return self::PERMISSION_ALLOW_BASIC;
    }

    /**
     * @return string
     */
    protected function getModelAccessKey(): string {
        return 'homepage';
    }

}
