<?php

namespace FKSDB\Models\ORM\Columns\Tables\Fyziklani\FyziklaniTeam;

use FKSDB\Models\ORM\Columns\ColumnFactory;
use FKSDB\Models\ORM\Models\Fyziklani\ModelFyziklaniTeam;
use Fykosak\NetteORM\AbstractModel;
use Nette\Utils\Html;

class NameNIdColumnFactory extends ColumnFactory
{

    /**
     * @param AbstractModel|ModelFyziklaniTeam $model
     * @return Html
     */
    protected function createHtmlValue(AbstractModel $model): Html
    {
        return Html::el('span')->addText($model->name . ' (' . $model->e_fyziklani_team_id . ')');
    }
}
