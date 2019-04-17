<?php

namespace EventModule;

use Events\Model\ApplicationHandlerFactory;
use Events\Model\Grid\SingleEventSource;
use FKSDB\Components\Events\ApplicationComponent;
use FKSDB\Components\Grids\Events\Application\AbstractApplicationGrid;
use FKSDB\Logging\FlashDumpFactory;
use FKSDB\Logging\MemoryLogger;
use FKSDB\ORM\Models\Fyziklani\ModelFyziklaniTeam;
use FKSDB\ORM\Models\ModelEventParticipant;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;

/**
 * Class ApplicationPresenter
 * @package EventModule
 */
abstract class AbstractApplicationPresenter extends BasePresenter {
    const TEAM_EVENTS = [1, 9];
    /**
     * @var ModelEventParticipant|ModelFyziklaniTeam
     */
    protected $model;
    /**
     * @var ApplicationHandlerFactory
     */
    protected $applicationHandlerFactory;
    /**
     * @var FlashDumpFactory
     */
    protected $dumpFactory;

    /**
     * @param ApplicationHandlerFactory $applicationHandlerFactory
     */
    public function injectHandlerFactory(ApplicationHandlerFactory $applicationHandlerFactory) {
        $this->applicationHandlerFactory = $applicationHandlerFactory;
    }

    /**
     * @param FlashDumpFactory $dumpFactory
     */
    public function injectFlashDumpFactory(FlashDumpFactory $dumpFactory) {
        $this->dumpFactory = $dumpFactory;
    }

    /**
     * @return ApplicationComponent
     * @throws BadRequestException
     * @throws \Nette\Application\AbortException
     */
    public function createComponentApplicationComponent() {
        $holders = [];
        $handlers = [];
        $flashDump = $this->dumpFactory->create('application');
        $source = new SingleEventSource($this->getEvent(), $this->container);
        foreach ($source as $key => $holder) {
            $holders[$key] = $holder;
            $handlers[$key] = $this->applicationHandlerFactory->create($this->getEvent(), new MemoryLogger()); //TODO it's a bit weird to create new logger for each handler
        }

        $component = new ApplicationComponent($handlers[$this->model->getPrimary()], $holders[$this->model->getPrimary()], $flashDump);
        return $component;
    }

    /**
     * @return bool
     * @throws BadRequestException
     * @throws \Nette\Application\AbortException
     */
    protected function isTeamEvent(): bool {
        if (\in_array($this->getEvent()->event_type_id, self::TEAM_EVENTS)) {
            $this->setAuthorized(false);
            return true;
        }
        return false;
    }

    /**
     * @param $id
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     * @throws \Nette\Application\AbortException
     */
    public function actionDetail($id) {
        $this->loadModel($id);
    }

    /**
     * @throws BadRequestException
     * @throws \Nette\Application\AbortException
     */
    public function renderList() {
        $this->template->event = $this->getEvent();
    }

    /**
     * @return void
     */
    abstract public function titleList();

    /**
     * @return void
     */
    abstract public function titleDetail();

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\BadRequestException
     * @return void;
     */
    abstract public function authorizedDetail();

    /**
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\BadRequestException
     * @return void;
     */
    abstract public function authorizedList();

    /**
     * @param int $id
     * @throws BadRequestException
     * @throws ForbiddenRequestException
     * @throws \Nette\Application\AbortException
     */
    abstract protected function loadModel(int $id);

    /**
     * @return ModelEventParticipant|ModelFyziklaniTeam
     */
    abstract protected function getModel();

    /**
     * @return AbstractApplicationGrid
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\BadRequestException
     */
    abstract function createComponentGrid(): AbstractApplicationGrid;
}
