<?php
use Nette\Object;

class YearCalculator extends Object {

    const YEAR = 31557600; //365.25*24*3600

    public function getCurrentYear($contest_id) {
        switch ($contest_id) {
            case ModelContest::ID_FYKOS:
                return ceil((time() - strtotime('1987-09-01')) / self::YEAR);
            case ModelContest::ID_VYFUK:
                return ceil((time() - strtotime('2011-09-01')) / self::YEAR);
            default:
                return null;
        }
    }

}
