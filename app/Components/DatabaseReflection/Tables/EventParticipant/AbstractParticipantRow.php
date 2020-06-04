<?php

namespace FKSDB\Components\DatabaseReflection\EventParticipant;

use FKSDB\Components\DatabaseReflection\AbstractRow;
use Nette\Forms\Controls\BaseControl;
use FKSDB\Exceptions\NotImplementedException;

/**
 * Class AbstractParticipantRow
 * @author Michal Červeňák <miso@fykos.cz>
 */
abstract class AbstractParticipantRow extends AbstractRow {

    /**
     * @param array $args
     * @return BaseControl
     * @throws NotImplementedException
     */
    public function createField(...$args): BaseControl {
        throw new NotImplementedException();
    }

    public function getPermissionsValue(): int {
        return self::PERMISSION_USE_GLOBAL_ACL;
    }
}
