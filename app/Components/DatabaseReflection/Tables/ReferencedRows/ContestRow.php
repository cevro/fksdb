<?php

namespace FKSDB\Components\DatabaseReflection\ReferencedRows;

use FKSDB\Components\Controls\Helpers\Badges\ContestBadge;
use FKSDB\Components\DatabaseReflection\AbstractRow;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\Models\IContestReferencedModel;
use Nette\Application\BadRequestException;
use Nette\Utils\Html;

/**
 * Class ContestRow
 * @package FKSDB\Components\DatabaseReflection\ReferencedRows
 */
class ContestRow extends AbstractRow {

    /**
     * @inheritDoc
     * @throws BadRequestException
     */
    protected function createHtmlValue(AbstractModelSingle $model): Html {
        if (!$model instanceof IContestReferencedModel) {
            throw new BadRequestException();
        }
        return ContestBadge::getHtml($model->getContest()->contest_id);
    }

    /**
     * @inheritDoc
     */
    public function getPermissionsValue(): int {
        return self::PERMISSION_USE_GLOBAL_ACL;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string {
        return _('Contest');
    }
}
