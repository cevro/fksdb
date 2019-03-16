<?php

/*
 * For presenters that provide contest and year context.
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */

use FKSDB\ORM\Models\ModelContest;

/**
 * Interface IContestPresenter
 */
interface IContestPresenter {

    /** @return \FKSDB\ORM\Models\ModelContest */
    public function getSelectedContest();

    /** @return int */
    public function getSelectedYear();

    /** @return int */
    public function getSelectedAcademicYear();
}

