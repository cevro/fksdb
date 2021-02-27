<?php

namespace FKSDB\Modules\OrgModule\Warehouse;

use FKSDB\Components\Grids\Warehouse\ItemsGrid;
use FKSDB\Models\Exceptions\NotImplementedException;
use FKSDB\Modules\Core\PresenterTraits\EntityPresenterTrait;
use FKSDB\Models\ORM\Services\Warehouse\ServiceItem;
use FKSDB\Models\UI\PageTitle;
use Nette\Application\UI\Control;
use Nette\Security\Resource;

/**
 * Class ItemPresenter
 * @author Michal Červeňák <miso@fykos.cz>
 */
class ItemPresenter extends BasePresenter {
    use EntityPresenterTrait;

    private ServiceItem $serviceItem;

    public function injectService(ServiceItem $serviceItem): void {
        $this->serviceItem = $serviceItem;
    }

    public function titleList(): void {
        $this->setPageTitle(new PageTitle(_('Items'), 'fa fa-boxes'));
    }

    public function titleEdit(): void {
        $this->setPageTitle(new PageTitle(_('Edit item'), 'fa fa-box-open'));
    }

    public function titleCreate(): void {
        $this->setPageTitle(new PageTitle(_('Create item'), 'fa fa-box-open'));
    }

    protected function createComponentCreateForm(): Control {
        throw new NotImplementedException();
    }

    protected function createComponentEditForm(): Control {
        throw new NotImplementedException();
    }

    protected function createComponentGrid(): ItemsGrid {
        return new ItemsGrid($this->getContext(), $this->getSelectedContest());
    }

    protected function getORMService(): ServiceItem {
        return $this->serviceItem;
    }

    /**
     * @param Resource|string|null $resource
     * @param string|null $privilege
     * @return bool
     */
    protected function traitIsAuthorized($resource, ?string $privilege): bool {
        return $this->isAllowed($resource, $privilege);
    }
}
