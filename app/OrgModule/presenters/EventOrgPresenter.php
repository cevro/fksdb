<?php

namespace OrgModule;

use FKSDB\Components\Forms\Containers\ModelContainer;
use FKSDB\Components\Grids\EventOrgsGrid;
use ModelEvent;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Persons\ExtendedPersonHandler;
use ServiceEvent;
use ServiceEventOrg;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Lukáš Timko <lukast@fykos.cz>
 */
class EventOrgPresenter extends ExtendedPersonPresenter {

    protected $modelResourceId = 'eventOrg';
    protected $fieldsDefinition = 'adminEventOrg';

    /**
     * @var ServiceEventOrg
     */
    private $serviceEventOrg;

    /**
     * @var ServiceEvent
     */
    private $serviceEvent;

    /**
     * @var ModelEvent
     */
    private $modelEvent = false;

    /**
     * @persistent
     */
    public $eventId;

    public function injectServiceEventOrg(ServiceEventOrg $serviceEventOrg) {
        $this->serviceEventOrg = $serviceEventOrg;
    }

    public function injectServiceEvent(ServiceEvent $serviceEvent) {
        $this->serviceEvent = $serviceEvent;
    }

    public function authorizedList() {
        $event = $this->getEvent($this->eventId);
        if (!$event) {
            throw new BadRequestException(_('Neexistující akce.'), 404);
        }
        parent::authorizedList();
    }

    public function authorizedCreate() {
        $event = $this->getEvent($this->eventId);
        if (!$event) {
            throw new BadRequestException(_('Neexistující akce.'), 404);
        }
        parent::authorizedCreate();
    }

    public function authorizedEdit($id) {
        $model = $this->getModel();
        if (!$model) {
            throw new BadRequestException(_('Neexistující organizátor akce.'), 404);
        }
        parent::authorizedEdit($id);
    }

    public function authorizedDelete($id) {
        $model = $this->getModel();
        if (!$model) {
            throw new BadRequestException(_('Neexistující organizátor akce.'), 404);
        }
        parent::authorizedDelete($id);
    }

    public function titleEdit($id) {
        $model = $this->getModel();
        $this->setTitle(sprintf(_('Úprava organizátora %s akce %s'), $model->getPerson()->getFullname(), (string) $model->getEvent()));
    }

    public function renderEdit($id) {
        parent::renderEdit($id);

        $eventOrg = $this->getModel();

        if ($eventOrg->event_id != $this->eventId) {
            $this->flashMessage(_('Editace organizátora akce mimo zvolenou akci.'), self::FLASH_ERROR);
            $this->redirect('list');
        }
    }

    public function titleCreate() {
        $this->setTitle(sprintf(_('Založit organizátora akce %s'), (string) $this->getEvent()));
    }

    public function titleList() {
        $this->setTitle(sprintf(_('Organizátoři akce %s'), (string) $this->getEvent()));
    }

    public function actionDelete($id) {
        $success = $this->serviceEventOrg->getTable()->where('e_org_id', $id)->delete();
        if ($success) {
            $this->flashMessage(_('Organizátor akce smazán.'), self::FLASH_SUCCESS);
        } else {
            $this->flashMessage(_('Nepodařilo se smazat organizátora akce.'), self::FLASH_ERROR);
        }
        $this->redirect('list');
    }

    protected function createComponentGrid($name) {
        return new EventOrgsGrid($this->getEvent(), $this->serviceEventOrg);
    }

    protected function appendExtendedContainer(Form $form) {
        $container = new ModelContainer();
        $container->setCurrentGroup(null);
        $container->addText('note', _('Poznámka'));
        $container->addHidden('event_id', $this->getEvent()->getPrimary());
        $form->addComponent($container, ExtendedPersonHandler::CONT_MODEL);
    }

    protected function getORMService() {
        return $this->serviceEventOrg;
    }

    public function messageCreate() {
        return _('Organizátor akce %s založen.');
    }

    public function messageEdit() {
        return _('Organizátor akce %s upraven.');
    }

    public function messageError() {
        return _('Chyba při zakládání organizátora akce.');
    }

    public function messageExists() {
        return _('Organizátor akce již existuje.');
    }

    /**
     * 
     * @return ModelEvent
     */
    private function getEvent($eventId = null) {
        if ($this->modelEvent === false) {
            if ($eventId) {
                $this->modelEvent = $this->serviceEvent->findByPrimary($eventId);
            } else {
                $model = $this->getModel();
                $this->modelEvent = $model ? $model->getEvent() : null;
            }
        }
        return $this->modelEvent;
    }

}