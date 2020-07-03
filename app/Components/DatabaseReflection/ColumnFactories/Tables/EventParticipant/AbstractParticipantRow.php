<?php

namespace FKSDB\Components\DatabaseReflection\EventParticipant;

use FKSDB\Components\DatabaseReflection\ColumnFactories\AbstractColumnFactory;
use FKSDB\Components\DatabaseReflection\FieldLevelPermission;
use FKSDB\Components\DatabaseReflection\OmittedControlException;
use Nette\Forms\Controls\BaseControl;

/**
 * Class AbstractParticipantRow
 * @author Michal Červeňák <miso@fykos.cz>
 */
abstract class AbstractParticipantRow extends AbstractColumnFactory {

    public function createField(...$args): BaseControl {
        throw new OmittedControlException();
    }

    public function getPermission(): FieldLevelPermission {
        return new FieldLevelPermission(self::PERMISSION_ALLOW_ANYBODY, self::PERMISSION_ALLOW_ANYBODY);
    }
}
