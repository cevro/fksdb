<?php

namespace FKSDB\Models\ORM\Columns\Types\DateTime;

use FKSDB\Components\Forms\Controls\DateInputs\DateInput;
use Nette\Forms\Controls\BaseControl;

/**
 * Class DateTimeRow
 * @author Michal Červeňák <miso@fykos.cz>
 */
class DateColumnFactory extends AbstractDateTimeColumnFactory {

    protected function createFormControl(...$args): BaseControl {
        return new DateInput($this->getTitle());
    }

    protected function getDefaultFormat(): string {
        return _('__date');
    }
}
