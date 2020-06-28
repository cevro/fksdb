<?php

namespace Persons;

use FKSDB\Components\Forms\Controls\IReferencedHandler;
use FKSDB\Components\Forms\Controls\ModelDataConflictException;
use FKSDB\Components\Forms\Controls\Schedule\FullCapacityException;
use FKSDB\Exceptions\NotImplementedException;
use FKSDB\ORM\AbstractServiceMulti;
use FKSDB\ORM\AbstractServiceSingle;
use FKSDB\Components\Forms\Controls\Schedule\ExistingPaymentException;
use FKSDB\Components\Forms\Controls\Schedule\Handler;
use FKSDB\ORM\IModel;
use FKSDB\ORM\IService;
use FKSDB\ORM\Models\ModelPerson;
use FKSDB\ORM\Models\ModelPersonHistory;
use FKSDB\ORM\Models\ModelPersonInfo;
use FKSDB\ORM\Models\ModelPostContact;
use FKSDB\ORM\Services\ServicePerson;
use FKSDB\ORM\Services\ServicePersonHistory;
use FKSDB\ORM\Services\ServicePersonInfo;
use FKSDB\Submits\StorageException;
use FKSDB\Utils\FormUtils;
use FKSDB\Exceptions\ModelException;
use FKSDB\ORM\ModelsMulti\ModelMPostContact;
use Nette\InvalidArgumentException;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;
use Nette\Utils\JsonException;
use FKSDB\ORM\ServicesMulti\ServiceMPersonHasFlag;
use FKSDB\ORM\ServicesMulti\ServiceMPostContact;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ReferencedPersonHandler implements IReferencedHandler {
    use SmartObject;

    const POST_CONTACT_DELIVERY = 'post_contact_d';
    const POST_CONTACT_PERMANENT = 'post_contact_p';

    /** @var ServicePerson */
    private $servicePerson;

    /** @var ServicePersonInfo */
    private $servicePersonInfo;

    /** @var ServicePersonHistory */
    private $servicePersonHistory;

    /** @var ServiceMPostContact */
    private $serviceMPostContact;

    /** @var ServiceMPersonHasFlag */
    private $serviceMPersonHasFlag;

    /** @var int */
    private $acYear;

    /** @var int */
    private $eventId;

    /** @var string */
    private $resolution;

    /** @var Handler */
    private $eventScheduleHandler;

    /**
     * ReferencedPersonHandler constructor.
     * @param ServicePerson $servicePerson
     * @param ServicePersonInfo $servicePersonInfo
     * @param ServicePersonHistory $servicePersonHistory
     * @param ServiceMPostContact $serviceMPostContact
     * @param ServiceMPersonHasFlag $serviceMPersonHasFlag
     * @param Handler $eventScheduleHandler
     * @param int $acYear
     * @param string $resolution
     */
    public function __construct(
        ServicePerson $servicePerson,
        ServicePersonInfo $servicePersonInfo,
        ServicePersonHistory $servicePersonHistory,
        ServiceMPostContact $serviceMPostContact,
        ServiceMPersonHasFlag $serviceMPersonHasFlag,
        Handler $eventScheduleHandler,
        int $acYear,
        $resolution
    ) {
        $this->servicePerson = $servicePerson;
        $this->servicePersonInfo = $servicePersonInfo;
        $this->servicePersonHistory = $servicePersonHistory;
        $this->serviceMPostContact = $serviceMPostContact;
        $this->serviceMPersonHasFlag = $serviceMPersonHasFlag;
        $this->acYear = $acYear;
        $this->resolution = $resolution;
        $this->eventScheduleHandler = $eventScheduleHandler;
    }

    public function getResolution(): string {
        return $this->resolution;
    }

    /**
     * @param string $resolution
     * @return void
     */
    public function setResolution(string $resolution) {
        $this->resolution = $resolution;
    }

    /**
     * @param ArrayHash $values
     * @return ModelPerson
     * @throws ExistingPaymentException
     * @throws FullCapacityException
     * @throws JsonException
     * @throws NotImplementedException
     */
    public function createFromValues(ArrayHash $values): ModelPerson {
        $email = isset($values['person_info']['email']) ? $values['person_info']['email'] : null;
        $person = $this->servicePerson->findByEmail($email);
        $person = $this->storePerson($person, (array)$values);
        $this->store($person, $values);
        return $person;
    }

    /**
     * @param IModel $model
     * @param ArrayHash $values
     * @return void
     * @throws ExistingPaymentException
     * @throws FullCapacityException
     * @throws JsonException
     * @throws NotImplementedException
     */
    public function update(IModel $model, ArrayHash $values) {
        /** @var ModelPerson $model */
        $this->store($model, $values);
    }

    /**
     * @param int $eventId
     * @return void
     */
    public function setEventId(int $eventId) {
        $this->eventId = $eventId;
    }

    /**
     * @param ModelPerson $person
     * @param ArrayHash $data
     * @return void
     * @throws ModelException
     * @throws ModelDataConflictException
     * @throws JsonException
     * @throws ExistingPaymentException
     * @throws StorageException
     * @throws FullCapacityException
     * @throws NotImplementedException
     */
    private function store(ModelPerson &$person, ArrayHash $data) {
        /*
         * Process data
         */
        try {
            $this->beginTransaction();
            /*
             * Person & its extensions
             */

            $models = [
                'person' => &$person,
                'person_info' => $person->getInfo() ?: null,
                'person_history' => $person->getHistory($this->acYear) ?: null,
                'person_schedule' => (($this->eventId && isset($data['person_schedule']) && $person->getSerializedSchedule($this->eventId, \array_keys((array)$data['person_schedule'])[0])) ?: null),
                self::POST_CONTACT_DELIVERY => ($dataPostContact = $person->getDeliveryAddress()) ?: $this->serviceMPostContact->createNew(['type' => ModelPostContact::TYPE_DELIVERY]),
                self::POST_CONTACT_PERMANENT => ($dataPostContact = $person->getPermanentAddress(true)) ?: $this->serviceMPostContact->createNew(['type' => ModelPostContact::TYPE_PERMANENT]),
            ];
            /**
             * @var IService[]|null[] $services
             */
            $services = [
                self::POST_CONTACT_DELIVERY => $this->serviceMPostContact,
                self::POST_CONTACT_PERMANENT => $this->serviceMPostContact,
            ];

            $originalModels = \array_keys(iterator_to_array($data));

            $this->prepareFlagServices($data, $services);
            $this->prepareFlagModels($person, $data, $models);

            $this->preparePostContactModels($models);
            $this->resolvePostContacts($data);

            $data = FormUtils::emptyStrToNull($data);
            $data = FormUtils::removeEmptyHashes($data);
            $conflicts = $this->getConflicts($models, $data);
            if ($this->resolution === self::RESOLUTION_EXCEPTION) {
                if (count($conflicts)) {
                    throw new ModelDataConflictException($conflicts);
                }
            } elseif ($this->resolution === self::RESOLUTION_KEEP) {
                $data = $this->removeConflicts($data, $conflicts);
            }
            // It's like this: $this->resolution == self::RESOLUTION_OVERWRITE) {
            //    $data = $conflicts;

            foreach ($models as $t => & $model) {

                if ($t === 'person') {
                    $model = $this->storePerson($model, (array)$data);
                    continue;
                } elseif ($t === 'person_info') {
                    $this->storePersonInfo($models['person'], $model, (array)$data);
                    continue;
                } elseif ($t === 'person_history') {
                    $this->storePersonHistory($models['person'], $model, (array)$data);
                    continue;
                } elseif ($t == 'person_schedule' && isset($data[$t])) {
                    $this->eventScheduleHandler->prepareAndUpdate($data[$t], $models['person'], $this->eventId);
                    continue;
                }

                if (!isset($data[$t])) {
                    if (\in_array($t, $originalModels) && \in_array($t, [self::POST_CONTACT_DELIVERY, self::POST_CONTACT_PERMANENT])) {
                        // delete only post contacts, other "children" could be left all-nulls
                        $services[$t]->dispose($model);
                    }
                    continue;
                }


                $data[$t]['person_id'] = $models['person']->person_id; // this works even for person itself
                // if ($services[$t] instanceof AbstractServiceSingle) {
                //     $services[$t]->updateModel2($model, $data[$t]);
                //  } else {
                $services[$t]->updateModel($model, $data[$t]);
                $services[$t]->save($model);
                //  }
            }

            $this->commit();
        } catch (ModelDataConflictException $exception) {
            $this->rollback();
            throw $exception;
        } catch (ModelException $exception) {
            $this->rollback();
            throw $exception;
        } catch (StorageException $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    /** @var bool */
    private $outerTransaction = false;

    /**
     * @param mixed $model
     * @param ArrayHash $values
     * @return array
     */
    private function getConflicts($model, ArrayHash $values): array {
        $conflicts = [];
        foreach ($values as $key => $value) {
            if (isset($model[$key])) {
                if ($model[$key] instanceof IModel) {
                    $subConflicts = $this->getModelConflicts($model[$key], (array)$value);
                    if (count($subConflicts)) {
                        $conflicts[$key] = $subConflicts;
                    }
                } elseif (!is_null($model[$key]) && $model[$key] != $value) {
                    $conflicts[$key] = $value;
                }
            }
        }

        return $conflicts;
    }

    private function getModelConflicts(IModel $model, array $values): array {
        $conflicts = [];
        foreach ($values as $key => $value) {
            if (isset($model[$key]) && !is_null($model[$key]) && $model[$key] != $value) {
                $conflicts[$key] = $value;
            }
        }
        return $conflicts;
    }

    /**
     * @param array $data
     * @param ModelPerson|null $person
     * @return ModelPerson
     */
    private function storePerson($person, array $data): ModelPerson {
        $personData = (array)$data['person'];
        if ($person) {
            $this->servicePerson->updateModel2($person, $personData);
            return $person;
        } else {
            return $this->servicePerson->createNewModel($personData);
        }
    }

    /**
     * @param ModelPerson $person
     * @param ModelPersonInfo|null $info
     * @param array $data
     * @return void
     */
    private function storePersonInfo(ModelPerson $person, $info, array $data) {
        $personInfoData = (array)$data['person_info'];
        if ($info) {
            $this->servicePersonInfo->updateModel2($info, $personInfoData);
        } else {
            $personInfoData['person_id'] = $person->person_id;
            $this->servicePersonInfo->createNewModel($personInfoData);
        }
    }

    /**
     * @param ModelPerson $person
     * @param ModelPersonHistory|null $history
     * @param array $data
     * @return void
     */
    private function storePersonHistory(ModelPerson $person, $history, array $data) {
        $personHistoryData = (array)$data['person_history'];
        if ($history) {
            $this->servicePersonHistory->updateModel2($history, $personHistoryData);
        } else {
            $this->servicePersonHistory->createNewModel(array_merge($personHistoryData, [
                'ac_year' => $this->acYear,
                'person_id' => $person->person_id,
            ]));
        }
    }

    /**
     * @param ArrayHash $data
     * @param ArrayHash|array $conflicts
     * @return ArrayHash
     */
    private function removeConflicts($data, $conflicts) {
        $result = $data;
        foreach ($conflicts as $key => $value) {
            if (isset($data[$key])) {
                if ($data[$key] instanceof ArrayHash) {
                    $result[$key] = $this->removeConflicts($data[$key], $value);
                } else {
                    unset($data[$key]);
                }
            }
        }

        return $result;
    }

    /**
     * @param ModelMPostContact[] $models
     */
    private function preparePostContactModels(&$models) {
        if ($models[self::POST_CONTACT_PERMANENT]->isNew()) {
            $data = $models[self::POST_CONTACT_DELIVERY]->toArray();
            unset($data['post_contact_id']);
            unset($data['address_id']);
            unset($data['type']);
            $this->serviceMPostContact->updateModel($models[self::POST_CONTACT_PERMANENT], $data);
        }
    }

    /**
     * @param ArrayHash $data
     * @return void
     */
    private function resolvePostContacts(ArrayHash $data) {
        foreach ([self::POST_CONTACT_DELIVERY, self::POST_CONTACT_PERMANENT] as $type) {
            if (!isset($data[$type])) {
                continue;
            }
            $cleared = FormUtils::removeEmptyHashes(FormUtils::emptyStrToNull($data[$type]), true);
            if (!isset($cleared['address'])) {
                unset($data[$type]);
                continue;
            }
            $data[$type] = $data[$type]['address']; // flatten
            switch ($type) {
                case self::POST_CONTACT_DELIVERY:
                    $data[$type]['type'] = ModelPostContact::TYPE_DELIVERY;
                    break;
                case self::POST_CONTACT_PERMANENT:
                    $data[$type]['type'] = ModelPostContact::TYPE_PERMANENT;
                    break;
            }
        }
    }

    /**
     * @param ModelPerson $person
     * @param ArrayHash $data
     * @param $models
     * @throws ModelException
     */
    private function prepareFlagModels(ModelPerson &$person, ArrayHash &$data, &$models) {
        if (!isset($data['person_has_flag'])) {
            return;
        }

        foreach ($data['person_has_flag'] as $fid => $value) {
            if ($value === null) {
                continue;
            }

            $models[$fid] = ($flag = $person->getMPersonHasFlag($fid)) ?: $this->serviceMPersonHasFlag->createNew(['fid' => $fid]);

            $data[$fid] = new ArrayHash();
            $data[$fid]['value'] = $value;
        }
        unset($data['person_has_flag']);
    }

    /**
     * @param ArrayHash $data
     * @param IService[]|AbstractServiceSingle[]|AbstractServiceMulti[] $services
     */
    private function prepareFlagServices(ArrayHash &$data, &$services) {
        if (!isset($data['person_has_flag'])) {
            return;
        }

        foreach ($data['person_has_flag'] as $fid => $value) {
            $services[$fid] = $this->serviceMPersonHasFlag;
        }
    }

    private function beginTransaction() {
        $connection = $this->servicePerson->getConnection();
        if (!$connection->getPdo()->inTransaction()) {
            $connection->beginTransaction();
        } else {
            $this->outerTransaction = true;
        }
    }

    private function commit() {
        $connection = $this->servicePerson->getConnection();
        if (!$this->outerTransaction) {
            $connection->commit();
        }
    }

    private function rollback() {
        $connection = $this->servicePerson->getConnection();
        if (!$this->outerTransaction) {
            $connection->rollBack();
        }
        //else: TODO ? throw an exception?
    }

    public function isSecondaryKey(string $field): bool {
        return $field == 'person_info.email';
    }

    /**
     * @param string $field
     * @param mixed $key
     * @return ModelPerson|null|IModel
     */
    public function findBySecondaryKey(string $field, string $key) {
        if (!$this->isSecondaryKey($field)) {
            throw new InvalidArgumentException("'$field' is not a secondary key.");
        }
        return $this->servicePerson->findByEmail($key);
    }

}
