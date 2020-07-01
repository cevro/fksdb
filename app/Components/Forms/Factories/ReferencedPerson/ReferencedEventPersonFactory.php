<?php

namespace FKSDB\Components\Forms\Factories\ReferencedPerson;

use FKSDB\Components\Forms\Containers\AddressContainer;
use FKSDB\Components\Forms\Containers\Models\ReferencedContainer;
use FKSDB\Components\Forms\Controls\Autocomplete\PersonProvider;
use FKSDB\Components\Forms\Factories\AddressFactory;
use FKSDB\Components\Forms\Factories\FlagFactory;
use FKSDB\Components\Forms\Factories\PersonFactory;
use FKSDB\Components\Forms\Factories\PersonHistoryFactory;
use FKSDB\Components\Forms\Factories\PersonInfoFactory;
use FKSDB\Components\Forms\Factories\PersonScheduleFactory;
use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Models\ModelPerson;
use FKSDB\ORM\Models\ModelPostContact;
use FKSDB\ORM\Services\ServiceFlag;
use FKSDB\ORM\Services\ServicePerson;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\IControl;
use Nette\Utils\JsonException;
use Persons\IModifiabilityResolver;
use Persons\IVisibilityResolver;
use Persons\ReferencedPersonHandlerFactory;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Červeňák <miso@fykos.cz>
 */
class ReferencedEventPersonFactory extends AbstractReferencedPersonFactory {

    /**
     * @var PersonScheduleFactory
     */
    private $personScheduleFactory;
    /**
     * @var ModelEvent
     */
    private $event;

    /**
     * ReferencedEventPersonFactory constructor.
     * @param AddressFactory $addressFactory
     * @param FlagFactory $flagFactory
     * @param ServicePerson $servicePerson
     * @param PersonFactory $personFactory
     * @param ReferencedPersonHandlerFactory $referencedPersonHandlerFactory
     * @param PersonProvider $personProvider
     * @param ServiceFlag $serviceFlag
     * @param PersonInfoFactory $personInfoFactory
     * @param PersonHistoryFactory $personHistoryFactory
     * @param PersonScheduleFactory $personScheduleFactory
     */
    public function __construct(
        AddressFactory $addressFactory,
        FlagFactory $flagFactory,
        ServicePerson $servicePerson,
        PersonFactory $personFactory,
        ReferencedPersonHandlerFactory $referencedPersonHandlerFactory,
        PersonProvider $personProvider,
        ServiceFlag $serviceFlag,
        PersonInfoFactory $personInfoFactory,
        PersonHistoryFactory $personHistoryFactory,
        PersonScheduleFactory $personScheduleFactory
    ) {
        parent::__construct($addressFactory,
            $flagFactory,
            $servicePerson,
            $personFactory,
            $referencedPersonHandlerFactory,
            $personProvider,
            $serviceFlag,
            $personInfoFactory,
            $personHistoryFactory);
        $this->personScheduleFactory = $personScheduleFactory;
    }

    /**
     * @param ModelEvent $event
     * @return void
     */
    public function setEvent(ModelEvent $event) {
        $this->event = $event;
    }

    /**
     * @param array $fieldsDefinition
     * @param int $acYear
     * @param string $searchType
     * @param bool $allowClear
     * @param IModifiabilityResolver $modifiabilityResolver
     * @param IVisibilityResolver $visibilityResolver
     * @param int $e
     * @return ReferencedContainer
     * @throws \Exception
     */
    public function createReferencedPerson(array $fieldsDefinition, int $acYear, string $searchType, bool $allowClear, IModifiabilityResolver $modifiabilityResolver, IVisibilityResolver $visibilityResolver, $e = 0):ReferencedContainer {
        return parent::createReferencedPerson($fieldsDefinition, $acYear, $searchType, $allowClear, $modifiabilityResolver, $visibilityResolver, $this->event->event_id);
    }


    /**
     * @param $sub
     * @param $fieldName
     * @param $acYear
     * @param HiddenField|null $hiddenField
     * @param array $metadata
     * @return AddressContainer|BaseControl|null
     * @throws JsonException
     * @throws \Exception
     */
    public function createField(string $sub, string $fieldName, int $acYear, HiddenField $hiddenField = null, array $metadata = []) {
        switch ($sub) {
            case 'person_schedule':
                $control = $this->personScheduleFactory->createField($fieldName, $this->event);
                $this->appendMetadata($control, $hiddenField, $fieldName, $metadata);
                return $control;
            default:
                return parent::createField($sub, $fieldName, $acYear, $hiddenField, $metadata);
        }


    }

    /**
     * @param ModelPerson|null $person
     * @param $sub
     * @param $field
     * @param $acYear
     * @param $options
     * @return bool|ModelPostContact|mixed|null|string
     * @throws JsonException
     */
    protected function getPersonValue(ModelPerson $person = null, string $sub, string $field, int $acYear, $options) {
        if (!$person) {
            return null;
        }
        switch ($sub) {
            case 'person_schedule':
                return $person->getSerializedSchedule($this->event->event_id, $field);
            default:
                return parent::getPersonValue($person, $sub, $field, $acYear, $options);
        }
    }
}
