<?php

namespace FKSDB\Components\Forms\Factories\Events;

use FKSDB\Model\Events\Model\Holder\Field;

/**
 *
 * @author michal
 */
interface IOptionsProvider {
    public function getOptions(Field $field): array;
}
