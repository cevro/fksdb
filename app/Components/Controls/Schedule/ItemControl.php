<?php

namespace FKSDB\Components\Controls\Schedule;

use FKSDB\Components\Forms\Factories\TableReflectionFactory;
use FKSDB\ORM\Models\Schedule\ModelScheduleItem;
use Nette\Application\UI\Control;
use Nette\ComponentModel\IComponent;
use Nette\Localization\ITranslator;
use Nette\Templating\FileTemplate;

/**
 * Class DetailControl
 * @package FKSDB\Components\Forms\Controls\Payment
 * @property FileTemplate $template
 */
class ItemControl extends Control {
    /**
     * @var ModelScheduleItem
     */
    private $model;
    /**
     * @var ITranslator
     */
    private $translator;
    /**
     * @var TableReflectionFactory
     */
    private $tableReflectionFactory;

    /**
     * DetailControl constructor.
     * @param ITranslator $translator
     * @param TableReflectionFactory $tableReflectionFactory
     */
    public function __construct(ITranslator $translator, TableReflectionFactory $tableReflectionFactory) {
        parent::__construct();
        $this->translator = $translator;
        $this->tableReflectionFactory = $tableReflectionFactory;
    }

    /**
     * @param ModelScheduleItem $group
     */
    public function setItem(ModelScheduleItem $group) {
        $this->model = $group;
    }

    public function render() {
        $this->template->model = $this->model;
        $this->template->setTranslator($this->translator);
        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'ItemControl.latte');
        $this->template->render();
    }


    /**
     * @param string $name
     * @return IComponent|null
     * @throws \Exception
     */
    public function createComponent($name) {
        $printerComponent = $this->tableReflectionFactory->createComponent($name, 2048);
        if ($printerComponent) {
            return $printerComponent;
        }
        return parent::createComponent($name);
    }
}
