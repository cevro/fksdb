<?php

namespace FKSDB\Components\Controls\PhoneNumber\Region;

use FKSDB\Components\Controls\PhoneNumber\InvalidPhoneNumberException;
use Nette\Utils\Html;

/**
 * Class Slovakia
 * @package FKSDB\Components\Controls\PhoneNumber\Region
 */
class Slovakia extends AbstractRegion {
    /**
     * @return string
     */
    protected static function getPrefix(): string {
        return '+421';
    }

    /**
     * @return int
     */
    protected static function getNSN(): int {
        return 9;
    }

    /**
     * @return string
     */
    protected static function getISO3166(): string {
        return 'sk';
    }

    /**
     * @param string $number
     * @return Html
     * @throws InvalidPhoneNumberException
     */
    public static function create(string $number): Html {
        if (preg_match('/^\+421(\d{3})(\d{3})(\d{3})$/', $number, $matches)) {
            return self::createHtml( $matches[1] . ' ' . $matches[2] . ' ' . $matches[3]);
        }
        throw new InvalidPhoneNumberException('number not match');
    }
}
