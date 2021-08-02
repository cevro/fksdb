<?php

declare(strict_types=1);

namespace FKSDB\Models\ORM\Columns\Tables\EventParticipant;

use FKSDB\Components\Badges\NotSetBadge;
use FKSDB\Models\ORM\Columns\ColumnFactory;
use FKSDB\Models\ORM\Models\ModelEventParticipant;
use FKSDB\Models\ValuePrinters\StringPrinter;
use Fykosak\NetteORM\AbstractModel;
use Nette\Application\BadRequestException;
use Nette\Utils\Html;

class TeamColumnFactory extends ColumnFactory
{

    /**
     * @param ModelEventParticipant|AbstractModel $model
     * @return Html
     */
    protected function createHtmlValue(AbstractModel $model): Html
    {
        try {
            $team = $model->getFyziklaniTeam();
            return (new StringPrinter())($team->name);
        } catch (BadRequestException $exception) {
            return NotSetBadge::getHtml();
        }
    }
}
