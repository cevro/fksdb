<?php

namespace FKSDB\Models\ORM\Columns\Tables\Fyziklani\FyziklaniSubmit;

use FKSDB\Models\ORM\Columns\ColumnFactory;
use Fykosak\NetteORM\AbstractModel;
use FKSDB\Models\ORM\Models\Fyziklani\ModelFyziklaniSubmit;
use Nette\Utils\Html;

class PointsColumnFactory extends ColumnFactory {

    /**
     * @param AbstractModel|ModelFyziklaniSubmit $model
     */
    protected function createHtmlValue(AbstractModel $model): Html {
        $el = Html::el('span');
        if (!\is_null($model->points)) {
            return $el->addText($model->points);
        }
        return $el->addAttributes(['class' => 'badge badge-warning'])->addText(_('revoked'));
    }
}
