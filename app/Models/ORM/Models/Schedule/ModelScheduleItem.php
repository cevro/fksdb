<?php

declare(strict_types=1);

namespace FKSDB\Models\ORM\Models\Schedule;

use FKSDB\Models\ORM\DbNames;
use FKSDB\Models\ORM\Models\ModelEvent;
use FKSDB\Models\Payment\Price;
use FKSDB\Models\Payment\PriceCalculator\UnsupportedCurrencyException;
use FKSDB\Models\WebService\NodeCreator;
use FKSDB\Models\WebService\XMLHelper;
use Fykosak\NetteORM\AbstractModel;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\GroupedSelection;
use Nette\Security\Resource;

/**
 * @property-read ActiveRow schedule_group
 * @property-read float price_eur
 * @property-read float price_czk
 * @property-read int|null capacity
 * @property-read int schedule_item_id
 * @property-read int schedule_group_id
 * @property-read string name_cs
 * @property-read string name_en
 * @property-read int require_id_number
 * @property-read string description_cs
 * @property-read string description_en
 */
class ModelScheduleItem extends AbstractModel implements Resource, NodeCreator
{

    public const RESOURCE_ID = 'event.scheduleItem';

    public function getScheduleGroup(): ModelScheduleGroup
    {
        return ModelScheduleGroup::createFromActiveRow($this->schedule_group);
    }

    public function getEvent(): ModelEvent
    {
        return $this->getScheduleGroup()->getEvent();
    }

    /**
     * @throws UnsupportedCurrencyException
     */
    public function getPrice(string $currency): Price
    {
        switch ($currency) {
            case Price::CURRENCY_EUR:
                return new Price(+$this->price_eur, $currency);
            case Price::CURRENCY_CZK:
                return new Price(+$this->price_czk, $currency);
            default:
                throw new UnsupportedCurrencyException($currency);
        }
    }

    public function getInterested(): GroupedSelection
    {
        return $this->related(DbNames::TAB_PERSON_SCHEDULE);
    }

    /* ****** CAPACITY CALCULATION *******/

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function isUnlimitedCapacity(): bool
    {
        return is_null($this->getCapacity());
    }

    public function getUsedCapacity(): int
    {
        return $this->getInterested()->count();
    }

    private function calculateAvailableCapacity(): int
    {
        return ($this->getCapacity() - $this->getUsedCapacity());
    }

    public function hasFreeCapacity(): bool
    {
        if ($this->isUnlimitedCapacity()) {
            return true;
        }
        return $this->calculateAvailableCapacity() > 0;
    }

    /**
     * @throws \LogicException
     */
    public function getAvailableCapacity(): int
    {
        if ($this->isUnlimitedCapacity()) {
            throw new \LogicException(_('Unlimited capacity'));
        }
        return $this->calculateAvailableCapacity();
    }

    public function getLabel(): string
    {
        return $this->name_cs . '/' . $this->name_en;
    }

    public function __toString(): string
    {
        return $this->getLabel();
    }

    public function __toArray(): array
    {
        return [
            'scheduleGroupId' => $this->schedule_group_id,
            'price' => [
                'eur' => $this->price_eur,
                'czk' => $this->price_czk,
            ],
            'totalCapacity' => $this->capacity,
            'usedCapacity' => $this->getUsedCapacity(),
            'scheduleItemId' => $this->schedule_item_id,
            'label' => [
                'cs' => $this->name_cs,
                'en' => $this->name_en,
            ],
            'requireIdNumber' => $this->require_id_number,
            'description' => [
                'cs' => $this->description_cs,
                'en' => $this->description_en,
            ],
        ];
    }

    public function createXMLNode(\DOMDocument $document): \DOMElement
    {
        $node = $document->createElement('scheduleItem');
        $node->setAttribute('scheduleItemId', (string)$this->schedule_item_id);
        XMLHelper::fillArrayToNode([
                                       'scheduleGroupId' => $this->schedule_group_id,
                                       'totalCapacity' => $this->capacity,
                                       'usedCapacity' => $this->getUsedCapacity(),
                                       'scheduleItemId' => $this->schedule_item_id,
                                       'requireIdNumber' => $this->require_id_number,
                                   ], $document, $node);
        XMLHelper::fillArrayArgumentsToNode('lang', [
            'description' => [
                'cs' => $this->description_cs,
                'en' => $this->description_en,
            ],
            'name' => [
                'cs' => $this->name_cs,
                'en' => $this->name_en,
            ],
        ],                                  $document, $node);
        XMLHelper::fillArrayArgumentsToNode('currency', [
            'price' => [
                'eur' => $this->price_eur,
                'czk' => $this->price_czk,
            ],
        ],                                  $document, $node);
        return $node;
    }

    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }
}
