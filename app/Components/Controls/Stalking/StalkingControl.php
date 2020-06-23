<?php

namespace FKSDB\Components\Controls\Stalking;

use FKSDB\Components\Controls\Badges\ContestBadge;
use FKSDB\Components\Controls\Badges\NoRecordsBadge;
use FKSDB\Components\Controls\Badges\PermissionDeniedBadge;
use FKSDB\Components\Controls\BaseComponent;
use FKSDB\Components\DatabaseReflection\ValuePrinterComponent;
use FKSDB\Components\Forms\Factories\TableReflectionFactory;
use FKSDB\ORM\Models\ModelPerson;

/**
 * Class StalkingControl
 * @author Michal Červeňák <miso@fykos.cz>
 */
abstract class StalkingControl extends BaseComponent {

    /**
     * @var TableReflectionFactory
     */
    protected $tableReflectionFactory;

    /**
     * @param TableReflectionFactory $tableReflectionFactory
     * @return void
     */
    public function injectPrimary(TableReflectionFactory $tableReflectionFactory) {
        $this->tableReflectionFactory = $tableReflectionFactory;
    }

    /**
     * @param ModelPerson $person
     * @param int $userPermissions
     * @return void
     */
    public function beforeRender(ModelPerson $person, int $userPermissions) {
        $this->template->userPermissions = $userPermissions;
        $this->template->gender = $person->gender;
    }

    protected function createComponentContestBadge(): ContestBadge {
        return new ContestBadge($this->getContext());
    }

    protected function createComponentPermissionDenied(): PermissionDeniedBadge {
        return new PermissionDeniedBadge($this->getContext());
    }

    protected function createComponentNoRecords(): NoRecordsBadge {
        return new NoRecordsBadge($this->getContext());
    }

    protected function createComponentValuePrinter(): ValuePrinterComponent {
        return new ValuePrinterComponent($this->getContext());
    }
}
