<?php

namespace FKSDB\Components\DatabaseReflection\ReferencedRows;

use FKSDB\Components\DatabaseReflection\AbstractRow;
use FKSDB\Components\DatabaseReflection\ValuePrinters\PersonLink;
use FKSDB\Exceptions\BadTypeException;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\Models\IPersonReferencedModel;
use Nette\Application\BadRequestException;
use Nette\Application\UI\PresenterComponent;
use Nette\Utils\Html;

/**
 * Class PersonLinkRow
 * @package FKSDB\Components\DatabaseReflection\VirtualRows
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
     * @param AbstractModelSingle $model
     * @return Html
     * @throws BadRequestException
     */
    protected function createHtmlValue(AbstractModelSingle $model): Html {
        if (!$model instanceof IPersonReferencedModel) {
            throw new BadTypeException(IPersonReferencedModel::class, $model);
        }
        return (new PersonLink($this->presenterComponent))($model->getPerson());
    }
}
