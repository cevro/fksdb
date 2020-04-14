<?php

namespace FKSDB\ORM\Services;

use FKSDB\ORM\AbstractServiceSingle;
use FKSDB\ORM\DbNames;
use FKSDB\ORM\IModel;
use FKSDB\ORM\Models\ModelPerson;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class ServicePerson extends AbstractServiceSingle {

    /**
     * @return string
     */
    public function getModelClassName(): string {
        return ModelPerson::class;
    }

    /**
     * @return string
     */
    protected function getTableName(): string {
        return DbNames::TAB_PERSON;
    }

    /**
     * Syntactic sugar.
     *
     * @param mixed $email
     * @return \FKSDB\ORM\Models\ModelPerson|null
     */
    public function findByEmail($email) {
        if (!$email) {
            return null;
        }
        $result = $this->getTable()->where(':person_info.email', $email)->fetch();
        return $result ? ModelPerson::createFromActiveRow($result) : null;
    }

    /**
     * @param IModel|ModelPerson $model
     * @return mixed|void
     */
    public function save(IModel &$model) {
        if (is_null($model->gender)) {
            $model->inferGender();
        }
        return parent::save($model);
    }

}

