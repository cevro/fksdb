<?php

namespace FKSDB\Components\DatabaseReflection\EventParticipant;

use FKSDB\Components\DatabaseReflection\DefaultPrinterTrait;

/**
 * Class ArrivalTimeRow
 * @author Michal Červeňák <miso@fykos.cz>
 */
class ArrivalTimeRow extends AbstractParticipantRow {
    use DefaultPrinterTrait;

    public function getTitle(): string {
        return _('Arrival time');
    }

    protected function getModelAccessKey(): string {
        return 'arrival_time';
    }
}
