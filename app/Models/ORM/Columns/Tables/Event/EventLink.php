<?php

namespace FKSDB\Models\ORM\Columns\Tables\Event;

use FKSDB\Models\ORM\Columns\ColumnFactory;
use FKSDB\Models\ORM\MetaDataFactory;
use Fykosak\NetteORM\AbstractModel;
use Nette\Application\LinkGenerator;
use FKSDB\Models\ORM\Models\ModelEvent;
use Nette\Application\UI\InvalidLinkException;
use Nette\Utils\Html;

/**
 * Class EventLink
 * @author Michal Červeňák <miso@fykos.cz>
 */
class EventLink extends ColumnFactory {

    private LinkGenerator $linkGenerator;

    public function __construct(LinkGenerator $linkGenerator, MetaDataFactory $metaDataFactory) {
        parent::__construct($metaDataFactory);
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * @param ModelEvent|AbstractModel $model
     * @return Html
     * @throws InvalidLinkException
     */
    protected function createHtmlValue(AbstractModel $model): Html {
        return Html::el('a')->addAttributes(['href' => $this->linkGenerator->link(
            'Event:Dashboard:default', ['eventId' => $model->event_id]
        )])->addText($model->name);
    }
}
