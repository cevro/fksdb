<?php

namespace FKSDB\Models\ValuePrinters;

use Nette\Utils\Html;

class EmailPrinter extends AbstractValuePrinter {
    /**
     * @param null $value
     * @return Html
     */
    protected function getHtml($value): Html {
        return Html::el('a')->addAttributes(['href' => 'mailto:' . $value])->addText($value);
    }
}
