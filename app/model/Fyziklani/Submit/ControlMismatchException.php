<?php

namespace FKSDB\Fyziklani\Submit;

use Throwable;

/**
 * Class ControlMismatchException
 * @author Michal Červeňák <miso@fykos.cz>
 */
class ControlMismatchException extends TaskCodeException {

    public function __construct(int $code = 0, ?Throwable $previous = null) {
        parent::__construct(_('Chybně zadaný kód úlohy.'), $code, $previous);
    }
}
