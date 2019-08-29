<?php


namespace FKSDB\ORM\Models;

use FKSDB\ORM\AbstractModelSingle;
use Nette\Database\Table\ActiveRow;

/**
 * Class ModelPaymentAccommodation
 * @package FKSDB\ORM
 * @property-read ActiveRow payment
 * @property-read int payment_id
 * @property-read ActiveRow event_person_accommodation
 * @property-read int event_person_accommodation_id
 * @property-read int payment_accommodation_id
 * @deprecated
 */
class ModelPaymentAccommodation extends AbstractModelSingle {
    /**
     * @return ModelEventPersonAccommodation
     */
    public function getEventPersonAccommodation(): ModelEventPersonAccommodation {
        return ModelEventPersonAccommodation::createFromActiveRow($this->event_person_accommodation);
    }

}
