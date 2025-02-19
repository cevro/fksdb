<?php

declare(strict_types=1);

namespace FKSDB\Models\ORM\Models\Schedule;

use FKSDB\Models\ORM\DbNames;
use FKSDB\Models\ORM\Models\ModelEvent;
use FKSDB\Models\WebService\NodeCreator;
use FKSDB\Models\WebService\XMLHelper;
use Fykosak\NetteORM\AbstractModel;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\GroupedSelection;
use Nette\Security\Resource;

/**
 * @property-read int schedule_group_id
 * @property-read string schedule_group_type
 * @property-read int event_id
 * @property-read ActiveRow event
 * @property-read \DateTimeInterface start
 * @property-read \DateTimeInterface end
 * @property-read string name_cs
 * @property-read string name_en
 */
class ModelScheduleGroup extends AbstractModel implements Resource, NodeCreator
{

    public const RESOURCE_ID = 'event.scheduleGroup';

    public const TYPE_ACCOMMODATION = 'accommodation';
    public const TYPE_VISA = 'visa';
    public const TYPE_ACCOMMODATION_GENDER = 'accommodation_gender';
    public const TYPE_ACCOMMODATION_TEACHER = 'accommodation_teacher';
    public const TYPE_TEACHER_PRESENT = 'teacher_present';
    public const TYPE_WEEKEND = 'weekend';
    public const TYPE_WEEKEND_INFO = 'weekend_info';

    public const TYPE_DSEF_MORNING = 'dsef_morning';
    public const TYPE_DSEF_AFTERNOON = 'dsef_afternoon';

    public function getItems(): GroupedSelection
    {
        return $this->related(DbNames::TAB_SCHEDULE_ITEM);
    }

    public function getEvent(): ModelEvent
    {
        return ModelEvent::createFromActiveRow($this->event);
    }

    /**
     * Label include datetime from schedule group
     */
    public function getLabel(): string
    {
        return $this->name_cs . '/' . $this->name_en;
    }

    public function __toArray(): array
    {
        return [
            'scheduleGroupId' => $this->schedule_group_id,
            'scheduleGroupType' => $this->schedule_group_type,
            'label' => [
                'cs' => $this->name_cs,
                'en' => $this->name_en,
            ],
            'eventId' => $this->event_id,
            'start' => $this->start->format('c'),
            'end' => $this->end->format('c'),
        ];
    }

    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function createXMLNode(\DOMDocument $document): \DOMElement
    {
        $node = $document->createElement('scheduleGroup');
        $node->setAttribute('scheduleGroupId', (string)$this->schedule_group_id);
        XMLHelper::fillArrayToNode([
            'scheduleGroupId' => $this->schedule_group_id,
            'scheduleGroupType' => $this->schedule_group_type,
            'eventId' => $this->event_id,
            'start' => $this->start->format('c'),
            'end' => $this->end->format('c'),
        ], $document, $node);
        XMLHelper::fillArrayArgumentsToNode('lang', [
            'name' => [
                'cs' => $this->name_cs,
                'en' => $this->name_en,
            ],
        ], $document, $node);
        return $node;
    }
}
