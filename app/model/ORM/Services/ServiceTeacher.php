<?php

namespace FKSDB\ORM\Services;


use FKSDB\ORM\DbNames;
use FKSDB\ORM\DeprecatedLazyService;
use FKSDB\ORM\Models\ModelTeacher;
use Nette\Database\Context;
use Nette\Database\IConventions;

/**
 * @author Michal Červeňák <miso@fykos.cz>
 */
class ServiceTeacher extends AbstractServiceSingle {
    use DeprecatedLazyService;

    public function __construct(Context $connection, IConventions $conventions) {
        parent::__construct($connection, $conventions, DbNames::TAB_TEACHER, ModelTeacher::class);
    }

    public function store(?ModelTeacher $model, array $data): ModelTeacher {
        if (is_null($model)) {
            return $this->createNewModel($data);
        } else {
            $this->updateModel2($model, $data);
            return $this->refresh($model);
        }
    }
}
