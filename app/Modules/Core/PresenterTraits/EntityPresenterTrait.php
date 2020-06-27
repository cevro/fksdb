<?php

namespace FKSDB\Modules\Core\PresenterTraits;

use FKSDB\Components\Controls\Entity\IEditEntityForm;
use FKSDB\Components\Controls\FormControl\FormControl;
use FKSDB\Components\Grids\BaseGrid;
use FKSDB\Exceptions;
use FKSDB\Exceptions\BadTypeException;
use FKSDB\Exceptions\NotImplementedException;
use FKSDB\Messages\Message;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\AbstractServiceSingle;
use FKSDB\ORM\IModel;
use FKSDB\ORM\IService;
use FKSDB\UI\PageTitle;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Control;
use Nette\InvalidStateException;
use Nette\Security\IResource;

/**
 * Trait EntityTrait
 * @author Michal Červeňák <miso@fykos.cz>
 */
trait EntityPresenterTrait {
    /**
     * @var AbstractModelSingle|IModel
     */
    protected $model;
    /**
     * @var int
     * @persistent
     */
    public $id;

    /**
     * @return void
     */
    public function authorizedList() {
        $this->setAuthorized($this->traitIsAuthorized($this->getModelResource(), 'list'));
    }

    /**
     * @return void
     */
    public function authorizedCreate() {
        $this->setAuthorized($this->traitIsAuthorized($this->getModelResource(), 'create'));
    }

    /**
     * @return void
     * @throws InvalidStateException
     */
    public function authorizedEdit() {
        $this->setAuthorized($this->traitIsAuthorized($this->getEntity(), 'edit'));
    }

    /**
     * @return void
     * @throws InvalidStateException
     */
    public function authorizedDelete() {
        $this->setAuthorized($this->traitIsAuthorized($this->getEntity(), 'delete'));
    }

    /**
     * @return void
     * @throws InvalidStateException
     */
    public function authorizedDetail() {
        $this->setAuthorized($this->traitIsAuthorized($this->getEntity(), 'detail'));
    }
    /* ****************** TITLES ***************************** */
    /**
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     */
    public function titleList() {
        $this->setPageTitle($this->getTitleList());
    }

    public function getTitleList(): PageTitle {
        return new PageTitle(_('List of entities'), 'fa fa-table');
    }

    /**
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     */
    final public function titleCreate() {
        $this->setPageTitle($this->getTitleCreate());
    }

    public function getTitleCreate(): PageTitle {
        return new PageTitle(_('Create an entity'), 'fa fa-plus');
    }

    /**
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     */
    public function titleEdit() {
        $this->setPageTitle(new PageTitle(_('Edit an entity'), 'fa fa-pencil'));
    }

    /**
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     */
    public function titleDetail() {
        $this->setPageTitle(new PageTitle(_('Detail of the entity'), 'fa fa-eye'));
    }

    /**
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     */
    public function titleDelete() {
        $this->setPageTitle(new PageTitle(_('Delete an entity'), 'fa fa-minus'));
    }

    /**
     * @return AbstractModelSingle|IModel
     * @throws InvalidStateException
     */
    public function getEntity() {
        $id = $this->getParameter($this->getPrimaryParameterName());
        // protection for tests ev. change URL during app is running
        if ($this->model && $id !== $this->model->getPrimary()) {
            $this->model = null;
        }
        if (!$this->model) {
            $this->model = $this->getORMService()->findByPrimary($id);
        }
        if (!$this->model) {
            throw new InvalidStateException('Model neexistuje');
        }
        return $this->model;
    }

    /**
     * @throws BadRequestException
     */
    protected function traitActionEdit() {
        $component = $this->getComponent('editForm');
        if (!$component instanceof IEditEntityForm) {
            throw new BadTypeException(IEditEntityForm::class, $component);
        }
        $component->setModel($this->getEntity());
    }

    /**
     * @return array
     */
    public function traitHandleDelete() {
        $success = $this->getEntity()->delete();
        if (!$success) {
            throw new Exceptions\ModelException(_('Error during deleting'));
        }
        return [new Message(_('Entity has been deleted'), self::FLASH_SUCCESS)];
    }

    /**
     * @return FormControl
     * @throws BadRequestException
     * @throws NotImplementedException
     */
    abstract protected function createComponentCreateForm(): Control;

    /**
     * @return FormControl
     * @throws BadRequestException
     * @throws NotImplementedException
     */
    abstract protected function createComponentEditForm(): Control;

    /**
     * @throws NotImplementedException
     */
    abstract protected function createComponentGrid(): BaseGrid;

    /**
     * @return IService|AbstractServiceSingle
     */
    abstract protected function getORMService();

    protected function getModelResource(): string {
        return $this->getORMService()->getModelClassName()::RESOURCE_ID;
    }

    protected function getPrimaryParameterName(): string {
        return 'id';
    }

    /**
     * @param IResource|string $resource
     * @param string $privilege
     * @return bool
     */
    abstract protected function traitIsAuthorized($resource, string $privilege): bool;

    /**
     * @param null $name
     * @param null $default
     * @return mixed
     */
    abstract public function getParameter($name, $default = null);

    /**
     * @param bool $access
     * @return void
     */
    abstract public function setAuthorized(bool $access);

    /**
     * @param PageTitle $pageTitle
     * @return void
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     */
    abstract public function setPageTitle(PageTitle $pageTitle);
}
