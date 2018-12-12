<?php


namespace FKSDB\ORM\Services;

use AbstractServiceSingle;
use DbNames;
use FKSDB\EventPayment\Handler\DuplicateAccommodationPaymentException;
use FKSDB\EventPayment\Handler\EmptyDataException;
use FKSDB\ORM\ModelPayment;
use Nette\ArrayHash;

class ServicePaymentAccommodation extends AbstractServiceSingle {
    protected $tableName = DbNames::TAB_PAYMENT_ACCOMMODATION;
    protected $modelClassName = 'FKSDB\ORM\ModelPaymentAccommodation';

    /**
     * @param ArrayHash $data
     * @param ModelPayment $payment
     * @throws DuplicateAccommodationPaymentException
     * @throws EmptyDataException
     */
    public function prepareAndUpdate(ArrayHash $data, ModelPayment $payment) {
        $oldRows =$this->where('payment_id',$payment->payment_id);
        // $payment->getRelatedPersonAccommodation();

        $newAccommodationIds = $this->prepareData($data);
        if (count($newAccommodationIds) == 0) {
            throw new EmptyDataException(_('Nebola vybraná žiadá položka'));
        };
        /**
         * @var $row \FKSDB\ORM\ModelPaymentAccommodation
         */
        foreach ($oldRows as $row) {
            if (in_array($row->event_person_accommodation_id, $newAccommodationIds)) {
                // do nothing
                $index = array_search($row->event_person_accommodation_id, $newAccommodationIds);
                unset($newAccommodationIds[$index]);
            } else {
                $row->delete();
            }
        }
        foreach ($newAccommodationIds as $id) {
            try {
                /**
                 * @var $model \FKSDB\ORM\ModelPaymentAccommodation
                 */
                $model = $this->createNew(['payment_id' => $payment->payment_id, 'event_person_accommodation_id' => $id]);
                $this->save($model);
            } catch (\PDOException $e) {
                if ($e->getPrevious() && $e->getPrevious()->getCode() == 23000) {
                    throw new DuplicateAccommodationPaymentException(sprintf(
                        _('Ubytovanie "%s" má už vygenrovanú platbu.'),
                        $model->getEventPersonAccommodation()->getLabel()
                    ));
                }
                throw $e;
            }

        }
    }


    /**
     * @param ArrayHash $data
     * @return integer[]
     */
    private function prepareData(ArrayHash $data): array {
        //$data = (array)json_decode($data);

        return \array_keys(\array_filter((array)$data, function ($value) {
            return $value;
        }));
    }
}
