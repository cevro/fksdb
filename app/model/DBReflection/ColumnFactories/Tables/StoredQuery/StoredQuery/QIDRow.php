<?php

namespace FKSDB\DBReflection\ColumnFactories\StoredQuery\StoredQuery;

use FKSDB\DBReflection\ColumnFactories\DefaultColumnFactory;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextInput;

/**
 * Class QIDRow
 * @author Michal Červeňák <miso@fykos.cz>
 */
class QIDRow extends DefaultColumnFactory {

    protected function createFormControl(...$args): BaseControl {
        $control = new TextInput($this->getTitle());
        $control->setOption('description', _('Dotazy s QIDem nelze smazat a QID lze použít pro práva a trvalé odkazování.'))
            ->addCondition(Form::FILLED)
            ->addRule(Form::MAX_LENGTH, _('Název dotazu je moc dlouhý.'), 64)
            ->addRule(Form::PATTERN, _('QID can contain only english letters, numbers and dots.'), '[a-z][a-z0-9.]*');
        return $control;
    }
}
