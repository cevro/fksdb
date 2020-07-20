<?php

namespace Persons;

use FKSDB\Components\Controls\Entity\Person\PersonForm;
use FKSDB\Components\Controls\Entity\Person\PersonFormComponent;
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
use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Models\ModelPerson;
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

    /** @var ModelEvent */
    private $event;

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
     * @param ModelEvent $event
     * @return void
     */
    public function setEvent(ModelEvent $event) {
        $this->event = $event;
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
                'person_schedule' => (($this->event && isset($data['person_schedule']) && $person->getSerializedSchedule($this->event->event_id, \array_keys((array)$data['person_schedule'])[0])) ?: null),
                self::POST_CONTACT_DELIVERY => $person->getDeliveryAddress() ?: null,
                self::POST_CONTACT_PERMANENT => $person->getPermanentAddress(true) ?: null,
            ];
            /** @var IService[] $services */
            $services = [];

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
                if (!isset($data[$t])) {
                    if (\in_array($t, $originalModels) && \in_array($t, [self::POST_CONTACT_DELIVERY, self::POST_CONTACT_PERMANENT])) {
                        // delete only post contacts, other "children" could be left all-nulls
                        if ($model) {
                            $this->serviceMPostContact->dispose($model);
                        }
                    }
                    continue;
                }

                if ($t === 'person') {
                    $model = $this->storePerson($model, (array)$data);
                    continue;
                } elseif ($t === 'person_info') {
                    $this->servicePersonInfo->store($person, $model, (array)$data['person_info']);
                    continue;
                } elseif ($t === 'person_history') {
                    $this->servicePersonHistory->store($person, $model, (array)$data['person_history'], $this->acYear);
                    continue;
                } elseif ($t === 'person_schedule' && isset($data[$t])) {
                    $this->eventScheduleHandler->prepareAndUpdate($data[$t], $models['person'], $this->event);
                    continue;
                } elseif ($t === self::POST_CONTACT_PERMANENT || $t === self::POST_CONTACT_DELIVERY) {
                    $datum = (array)$data[$t];
                    $datum['person_id'] = $person->person_id;
                    if ($models[$t]) {
                        $this->serviceMPostContact->updateModel2($model, $datum);
                    } else {
                        $this->serviceMPostContact->createNewModel(array_merge($datum, ['type' => PersonFormComponent::mapAddressContainerNameToType($t)]));
                    }
                    continue;
                }

                $data[$t]['person_id'] = $models['person']->person_id; // this works even for person itself
                if ($services[$t] instanceof AbstractServiceSingle) {
                    $services[$t]->updateModel2($model, (array)$data[$t]);
                } else {
                    $services[$t]->updateModel($model, $data[$t]);
                    $services[$t]->save($model);
                }
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

    /**
     * @param ModelPerson $person
     * @param ModelMPostContact|null $model
     * @param array $data
     * @param string $type
     * @return void
     */
    private function storePostContact(ModelPerson $person, $model, array $data, string $type) {
        if (!$model) {
            $this->serviceMPostContact->createNewModel(array_merge((array)$data, [
                'person_id' => $person->person_id,
                'type' => $type === self::POST_CONTACT_DELIVERY ? ModelPostContact::TYPE_DELIVERY : ModelPostContact::TYPE_PERMANENT,
            ]));
        } else {
            $this->serviceMPostContact->updateModel2($model, $data);
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
        return $this->servicePerson->store($person, (array)$data['person']);
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
    private function preparePostContactModels(array &$models) {
        if (!$models[self::POST_CONTACT_PERMANENT] && $models[self::POST_CONTACT_DELIVERY]) {
            $data = $models[self::POST_CONTACT_DELIVERY]->toArray();
            unset($data['post_contact_id']);
            unset($data['address_id']);
            unset($data['type']);
            $models[self::POST_CONTACT_PERMANENT] = $this->serviceMPostContact->createNewModel(array_merge($data, ['type' => ModelPostContact::TYPE_PERMANENT]));
            //     $this->serviceMPostContact->updateModel($models[self::POST_CONTACT_PERMANENT], $data);
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
     * @param array $models
     * @throws ModelException
     */
    private function prepareFlagModels(ModelPerson $person, ArrayHash &$data, array &$models) {
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
    private function prepareFlagServices(ArrayHash $data, array &$services) {
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
