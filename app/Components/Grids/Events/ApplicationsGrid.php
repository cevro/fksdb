<?php

namespace FKSDB\Components\Events;

use FKSDB\Components\Controls\BaseComponent;
use FKSDB\Events\Machine\Machine;
use FKSDB\Events\Model\ApplicationHandler;
use FKSDB\Events\Model\ApplicationHandlerFactory;
use FKSDB\Events\Model\Grid\IHolderSource;
use FKSDB\Events\Model\Holder\Holder;
use FKSDB\Application\IJavaScriptCollector;
use FKSDB\Events\EventDispatchFactory;
use FKSDB\Logging\MemoryLogger;
use FKSDB\Modules\Core\BasePresenter;
use FKSDB\ORM\Models\ModelEvent;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\DI\Container;
use Nette\InvalidStateException;
use Nette\Utils\Strings;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 * @method BasePresenter getPresenter($need = true)
 */
class ApplicationsGrid extends BaseComponent {

    const NAME_PREFIX = 'application_';
    /** @var IHolderSource */
    private $source;

    /** @var Holder[] */
    private $holders = [];

    /** @var Machine[] */
    private $machines = [];

    /** @var ModelEvent[] */
    private $eventApplications = [];

    /** @var ApplicationHandler[] */
    private $handlers = [];

    /** @var ApplicationHandlerFactory */
    private $handlerFactory;

    /** @var string */
    private $templateFile;

    /** @var bool */
    private $searchable = false;
    /** @var bool */
    private $attachedJS = false;

    private EventDispatchFactory $eventDispatchFactory;

    /**
     * ApplicationsGrid constructor.
     * @param Container $container
     * @param IHolderSource $source
     * @param ApplicationHandlerFactory $handlerFactory
     *
     */
    public function __construct(Container $container, IHolderSource $source, ApplicationHandlerFactory $handlerFactory) {
        parent::__construct($container);
        $this->monitor(IJavaScriptCollector::class, function (IJavaScriptCollector $collector) {
            if (!$this->attachedJS) {
                $this->attachedJS = true;
                $collector->registerJSFile('js/searchTable.js');
            }
        });
        $this->source = $source;
        $this->handlerFactory = $handlerFactory;
        $this->processSource();
    }


    /**
     * @param string $template name of the standard template or whole path
     */
    public function setTemplate($template) {
        if (stripos($template, '.latte') !== false) {
            $this->templateFile = $template;
        } else {
            $this->templateFile = __DIR__ . DIRECTORY_SEPARATOR . "ApplicationsGrid.$template.latte";
        }
    }

    public function injectEventDispatchFactory(EventDispatchFactory $eventDispatchFactory): void {
        $this->eventDispatchFactory = $eventDispatchFactory;
    }

    /**
     * @return bool
     */
    public function isSearchable() {
        return $this->searchable;
    }

    /**
     * @param bool $searchable
     * @return void
     */
    public function setSearchable($searchable) {
        $this->searchable = $searchable;
    }

    /**
     * @return void
     *
     */
    private function processSource() {
        $this->eventApplications = [];

        foreach ($this->source->getHolders() as $key => $holder) {
            $event = $holder->getPrimaryHolder()->getEvent();
            $this->eventApplications[$key] = $event;
            $this->holders[$key] = $holder;
            $this->machines[$key] = $this->eventDispatchFactory->getEventMachine($event);
            $this->handlers[$key] = $this->handlerFactory->create($event, new MemoryLogger()); //TODO it's a bit weird to create new logger for each handler
        }
    }

    /**
     * @param string $name
     * @return ApplicationComponent|IComponent
     */
    protected function createComponent($name) {

        $key = null;
        if (Strings::startsWith($name, self::NAME_PREFIX)) {
            $key = substr($name, strlen(self::NAME_PREFIX));
        }
        if (!$key) {
            parent::createComponent($name);
        }
        return new ApplicationComponent($this->getContext(), $this->handlers[$key], $this->holders[$key]);
    }

    public function render() {
        if (!$this->templateFile) {
            throw new InvalidStateException('Must set template for the grid.');
        }

        $this->template->eventApplications = $this->eventApplications;
        $this->template->holders = $this->holders;
        $this->template->machines = $this->machines;
        $this->template->htmlId = $this->lookupPath(Presenter::class);

        $this->template->setFile($this->templateFile);
        $this->template->render();
    }

}
