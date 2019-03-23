<?php


namespace FKSDB\ValidationTest;

use Nette\Utils\Html;

/**
 * Class ValidationLog
 * @package FKSDB\ValidationTest
 */
class ValidationLog {
    /**
     * @var string
     */
    public $level;
    /**
     * @var string
     */
    public $message;
    /**
     * @var Html
     */
    public $detail;

    /**
     * ValidationLog constructor.
     * @param string $message
     * @param string $level
     * @param Html|null $detail
     */
    public function __construct(string $message, string $level, Html $detail = null) {
        $this->level = $level;
        $this->message = $message;
        $this->detail = $detail;
    }
}
