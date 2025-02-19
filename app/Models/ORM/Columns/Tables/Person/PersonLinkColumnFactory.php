<?php

namespace FKSDB\Models\ORM\Columns\Tables\Person;

use FKSDB\Models\ORM\Columns\ColumnFactory;
use FKSDB\Models\ORM\MetaDataFactory;
use FKSDB\Models\ValuePrinters\PersonLink;
use Fykosak\NetteORM\AbstractModel;
use FKSDB\Models\ORM\Models\ModelPerson;
use Nette\Application\LinkGenerator;
use Nette\Utils\Html;

class PersonLinkColumnFactory extends ColumnFactory {

    private LinkGenerator $presenterComponent;

    public function __construct(LinkGenerator $presenterComponent, MetaDataFactory $metaDataFactory) {
        parent::__construct($metaDataFactory);
        $this->presenterComponent = $presenterComponent;
    }

    /**
     * @param AbstractModel|ModelPerson $model
     */
    protected function createHtmlValue(AbstractModel $model): Html {
        return (new PersonLink($this->presenterComponent))($model);
    }
}
