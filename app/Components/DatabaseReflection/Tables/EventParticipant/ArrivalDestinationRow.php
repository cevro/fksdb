<?php

namespace FKSDB\Components\DatabaseReflection\EventParticipant;

/**
 * Class ArrivalDestinationRow
 * @package FKSDB\Components\DatabaseReflection\EventParticipant
 */
class ArrivalDestinationRow extends AbstractParticipantRow {
    /**
     * @return string
     */
    public function getTitle(): string {
        return _('Arrival destination');
    }
}
