<?php

namespace FKSDB\ORM;

use AbstractModelSingle;
use Nette\Database\Table\ActiveRow;
use Nette\Security\IResource;

/**
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 * @property ActiveRow person
 * @property ActiveRow contest
 */
class ModelContestant extends AbstractModelSingle implements IResource {

    /**
     * @return ModelPerson
     */
    public function getPerson() {
        $data = $this->person;
        return ModelPerson::createFromTableRow($data);
    }

    /**
     * @return ModelContest
     */
    public function getContest() {
        $data = $this->contest;
        return ModelContest::createFromTableRow($data);
    }

    public function getResourceId() {
        return 'contestant';
    }

}
