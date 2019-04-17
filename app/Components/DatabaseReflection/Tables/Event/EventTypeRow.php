<?php

namespace FKSDB\Components\DatabaseReflection\Event;

use FKSDB\Components\DatabaseReflection\AbstractRow;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\Models\ModelContest;
use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Services\ServiceEventType;
use Nette\Application\BadRequestException;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\SelectBox;
use Nette\Localization\ITranslator;
use Nette\Utils\Html;

/**
 * Class EventTypeRow
 * @package FKSDB\Components\DatabaseReflection\Event
 */
class EventTypeRow extends AbstractRow {
    /**
     * @var ServiceEventType
     */
    private $serviceEventType;

    /**
     * EventTypeRow constructor.
     * @param ITranslator $translator
     * @param ServiceEventType $serviceEventType
     */
    public function __construct(ITranslator $translator, ServiceEventType $serviceEventType) {
        parent::__construct($translator);
        $this->serviceEventType = $serviceEventType;
    }

    /**
     * @return int
     */
    public function getPermissionsValue(): int {
        return self::PERMISSION_USE_GLOBAL_ACL;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return _('Event type');
    }

    /**
     * @param ModelContest|null $contest
     * @return BaseControl
     * @throws BadRequestException
     */
    public function createField(ModelContest $contest = null): BaseControl {
        if (\is_null($contest)) {
            throw new BadRequestException();
        }

        $element = new SelectBox($this->getTitle());

        $types = $this->serviceEventType->getTable()->where('contest_id', $contest->contest_id)->fetchPairs('event_type_id', 'name');
        $element->setItems($types);
        $element->setPrompt(_('Zvolit typ'));

        return $element;
    }

    /**
     * @param AbstractModelSingle|ModelEvent $model
     * @param string $fieldName
     * @return Html
     */
    public function createHtmlValue(AbstractModelSingle $model, string $fieldName): Html {
        return Html::el('span')->addText($model->getEventType()->name);
    }

}
