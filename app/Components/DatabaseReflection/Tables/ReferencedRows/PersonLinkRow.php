<?php

namespace FKSDB\Components\DatabaseReflection\ReferencedRows;

use FKSDB\Components\DatabaseReflection\AbstractRow;
use FKSDB\Components\DatabaseReflection\ValuePrinters\PersonLink;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\Models\ModelPerson;
use Nette\Application\UI\PresenterComponent;
use Nette\Utils\Html;

/**
 * Class PersonLinkRow
 * @author Michal Červeňák <miso@fykos.cz>
 */
class PersonLinkRow extends AbstractRow {
    /**
     * @var PresenterComponent
     */
    private $presenterComponent;

    /**
     * PersonLinkRow constructor.
     * @param PresenterComponent $presenterComponent
     */
    public function __construct(PresenterComponent $presenterComponent) {
        $this->presenterComponent = $presenterComponent;
    }

    public function getPermissionsValue(): int {
        return self::PERMISSION_USE_GLOBAL_ACL;
    }

    public function getTitle(): string {
        return _('Person');
    }

    /**
     * @param AbstractModelSingle|ModelPerson $model
     * @return Html
     */
    protected function createHtmlValue(AbstractModelSingle $model): Html {
        return (new PersonLink($this->presenterComponent))($model);
    }
}
