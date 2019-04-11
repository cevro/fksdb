<?php

namespace FKSDB\Components\Controls\Helpers\ValuePrinters;

use FKSDB\Components\DatabaseReflection\ValuePrinters\AbstractValuePrinter;
use FKSDB\Components\DatabaseReflection\ValuePrinters\HashPrinter;
use Nette\Templating\FileTemplate;

/**
 * Class BinaryValueControl
 * @property FileTemplate $template
 */
class IsSetValueControl extends PrimitiveValueControl {

    /**
     * @return AbstractValuePrinter
     */
    protected static function getPrinter(): AbstractValuePrinter {
        return new HashPrinter;
    }
}
