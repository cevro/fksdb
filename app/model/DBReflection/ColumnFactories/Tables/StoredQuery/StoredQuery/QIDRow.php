<?php

namespace FKSDB\DBReflection\ColumnFactories\StoredQuery\StoredQuery;

use FKSDB\DBReflection\ColumnFactories\AbstractColumnFactory;
use FKSDB\DBReflection\FieldLevelPermission;
use FKSDB\ValuePrinters\StringPrinter;
use FKSDB\ORM\AbstractModelSingle;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * Class QIDRow
 * @author Michal Červeňák <miso@fykos.cz>
 */
class QIDRow extends AbstractColumnFactory {

    public function getTitle(): string {
        return _('QID');
    }

    public function getPermission(): FieldLevelPermission {
        return new FieldLevelPermission(self::PERMISSION_ALLOW_ANYBODY, self::PERMISSION_ALLOW_ANYBODY);
    }

    protected function createHtmlValue(AbstractModelSingle $model): Html {
        return (new StringPrinter())($model->qid);
    }

    public function createField(...$args): BaseControl {
        $control = new TextInput($this->getTitle());
        $control->setOption('description', _('Dotazy s QIDem nelze smazat a QID lze použít pro práva a trvalé odkazování.'))
            ->addCondition(Form::FILLED)
            ->addRule(Form::MAX_LENGTH, _('Název dotazu je moc dlouhý.'), 64)
            ->addRule(Form::PATTERN, _('QID can contain only english letters, numbers and dots.'), '[a-z][a-z0-9.]*');
        return $control;
    }
}
