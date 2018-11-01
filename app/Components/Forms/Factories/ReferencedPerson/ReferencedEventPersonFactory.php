<?php

namespace FKSDB\Components\Forms\Factories\ReferencedPerson;

use FKSDB\Components\Forms\Controls\Autocomplete\PersonProvider;
use FKSDB\Components\Forms\Factories\AddressFactory;
use FKSDB\Components\Forms\Factories\FlagFactory;
use FKSDB\Components\Forms\Factories\PersonAccommodationFactory;
use FKSDB\Components\Forms\Factories\PersonFactory;
use FKSDB\Components\Forms\Factories\PersonHistoryFactory;
use FKSDB\Components\Forms\Factories\PersonInfoFactory;
use FKSDB\ORM\ModelPerson;
use Nette\Forms\Controls\HiddenField;
use Persons\IModifiabilityResolver;
use Persons\IVisibilityResolver;
use Persons\ReferencedPersonHandlerFactory;
use ServiceFlag;
use ServicePerson;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Červeňák <miso@fykos.cz>
 */
class ReferencedEventPersonFactory extends AbstractReferencedPersonFactory {

    /**
     * @var PersonAccommodationFactory
     */
    private $personAccommodationFactory;
    /**
     * @var integer
     */
    private $eventId;

    public function __construct(
        PersonAccommodationFactory $personAccommodationFactory,
        AddressFactory $addressFactory,
        FlagFactory $flagFactory,
        ServicePerson $servicePerson,
        PersonFactory $personFactory,
        ReferencedPersonHandlerFactory $referencedPersonHandlerFactory,
        PersonProvider $personProvider,
        ServiceFlag $serviceFlag,
        PersonInfoFactory $personInfoFactory,
        PersonHistoryFactory $personHistoryFactory
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
        $this->personAccommodationFactory = $personAccommodationFactory;
    }

    public function setEventId($eventId) {
        $this->eventId = $eventId;
    }

    public function createReferencedPerson($fieldsDefinition, $acYear, $searchType, $allowClear, IModifiabilityResolver $modifiabilityResolver, IVisibilityResolver $visibilityResolver, $e = 0) {
        return parent::createReferencedPerson($fieldsDefinition, $acYear, $searchType, $allowClear, $modifiabilityResolver, $visibilityResolver, $this->eventId); // TODO: Change the autogenerated stub
    }


    public function createField($sub, $fieldName, $acYear, HiddenField $hiddenField = null, array $metadata = []) {

        if ($sub === 'person_accommodation') {
            $control = $this->personAccommodationFactory->createField($fieldName, $this->eventId);
            $this->appendMetadata($control, $hiddenField, $fieldName, $metadata);
            return $control;
        }
        return parent::createField($sub, $fieldName, $acYear, $hiddenField, $metadata);
    }

    protected function getPersonValue(ModelPerson $person, $sub, $field, $acYear, $options) {
        if (!$person) {
            return null;
        }
        if ($sub === 'person_accommodation') {
            return $person->getAccommodationByEventId($this->eventId);
        }
        return parent::getPersonValue($person, $sub, $field, $acYear, $options);
    }
}
