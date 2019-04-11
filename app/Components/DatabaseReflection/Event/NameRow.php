<?php

namespace FKSDB\Components\DatabaseReflection\Event;

use FKSDB\Components\DatabaseReflection\AbstractRow;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;

/**
 * Class NameRow
 * @package FKSDB\Components\DatabaseReflection\Event
 */
class NameRow extends AbstractRow {
    /**
     * @return int
     */
    public function getPermissionsValue(): int {
        return self::PERMISSION_USE_GLOBAL_ACL;
    }

    /**
     * @return string
     */
    public static function getTitle(): string {
        return _('Name');
    }

    /**
     * @return BaseControl
     */
    public function createField(): BaseControl {
        $control = parent::createField();
        $control->addRule(Form::FILLED, _('%label je povinný.'))
            ->addRule(Form::MAX_LENGTH, null, 255)
            ->setOption('description', _('U soustředka místo.'));
        return $control;
    }
}
