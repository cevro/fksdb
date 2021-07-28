<?php

namespace FKSDB\Models\ORM\Columns\Tables\Login;

use FKSDB\Models\ORM\Columns\ColumnFactory;
use FKSDB\Models\ValuePrinters\HashPrinter;
use Fykosak\NetteORM\AbstractModel;
use FKSDB\Models\ORM\Models\ModelLogin;
use Nette\Utils\Html;

class HashColumnFactory extends ColumnFactory {

    /**
     * @param AbstractModel|ModelLogin $model
     * @return Html
     */
    protected function createHtmlValue(AbstractModel $model): Html {
        return (new HashPrinter())($model->hash);
    }
}
