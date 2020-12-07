<?php

namespace FKSDB\Components\Forms\Rules;

use FKSDB\Model\ORM\Models\ModelPerson;
use FKSDB\Model\ORM\Services\ServicePersonInfo;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class UniqueEmailFactory {

    private ServicePersonInfo $servicePersonInfo;

    public function __construct(ServicePersonInfo $servicePersonInfo) {
        $this->servicePersonInfo = $servicePersonInfo;
    }

    public function create(?ModelPerson $person): UniqueEmail {
        $rule = new UniqueEmail($this->servicePersonInfo);
        $rule->setIgnoredPerson($person);
        return $rule;
    }
}
