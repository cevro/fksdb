<?php

namespace FKSDB\Components\Grids;

use Exception;
use FKSDB\Components\Controls\FormControl\FormControl;
use FKSDB\Components\Forms\Factories\TableReflectionFactory;
use FKSDB\Exceptions\BadTypeException;
use FKSDB\ORM\AbstractModelSingle;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Nette\InvalidStateException;
use FKSDB\Exceptions\NotImplementedException;
use Nette\Utils\Html;
use NiftyGrid\Components\Button;
use NiftyGrid\Components\Column;
use NiftyGrid\Components\GlobalButton;
use NiftyGrid\DataSource\IDataSource;
use NiftyGrid\DuplicateButtonException;
use NiftyGrid\DuplicateColumnException;
use NiftyGrid\DuplicateGlobalButtonException;
use NiftyGrid\Grid;
use NiftyGrid\GridException;
use NiftyGrid\GridPaginator;
use PePa\CSVResponse;
use SQL\SearchableDataSource;

/**
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
abstract class BaseGrid extends Grid {
    /** @persistent string */
    public $searchTerm;
    /**
     * @var TableReflectionFactory
     */
    protected $tableReflectionFactory;

    /**
     * BaseGrid constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct();
        $container->callInjects($this);
    }

    /**
     * @param TableReflectionFactory $tableReflectionFactory
     * @return void
     */
    public function injectTableReflectionFactory(TableReflectionFactory $tableReflectionFactory) {
        $this->tableReflectionFactory = $tableReflectionFactory;
    }

    /**
     * @param Presenter $presenter
     * @return void
     */
    protected function configure(Presenter $presenter) {
        try {
            $this->setDataSource($this->getData());
        } catch (NotImplementedException$exception) {

        }
        $this->setTemplate(__DIR__ . DIRECTORY_SEPARATOR . 'BaseGrid.latte');
        /** @var GridPaginator $paginator */
        $paginator = $this->getComponent('paginator');
        $paginator->setTemplate(__DIR__ . DIRECTORY_SEPARATOR . 'BaseGrid.paginator.latte');
    }

    /**
     * @return IDataSource
     * @throws NotImplementedException
     */
    protected function getData(): IDataSource {
        throw new NotImplementedException();
    }

    /**
     * @param null $class
     * @return ITemplate
     * @throws BadTypeException
     */
    protected function createTemplate(): ITemplate {
        $presenter = $this->getPresenter();
        if (!$presenter instanceof \BasePresenter) {
            throw new BadTypeException(\BasePresenter::class, $presenter);
        }

        /** @var GridPaginator $paginator */
        $paginator = $this->getComponent('paginator');
        $paginator->getTemplate()->setTranslator($presenter->getTranslator());
        $template = parent::createTemplate();
        $template->setTranslator($presenter->getTranslator());
        return $template;
    }

    /*     * *****************************
     * Extended rendering for the paginator
     * ***************************** */
    /**
     * @throws GridException
     */
    public function render() {
        $paginator = $this->getPaginator();

        // this has to be done already here (and in the parent call again :-( )
        if ($this->searchTerm) {
            $this->dataSource->applyFilter($this->searchTerm);
        }
        $count = $this->getCount();
        $this->getPaginator()->itemCount = $count;
        /*
         * Credits David Grudl.
         * @see http://addons.nette.org/cs/visualpaginator
         */
        $page = $paginator->page;
        if ($paginator->pageCount < 2) {
            $steps = [$page];
        } else {
            $arr = range(max($paginator->firstPage, $page - 3), min($paginator->lastPage, $page + 3));
            $count = 4;
            $quotient = ($paginator->pageCount - 1) / $count;
            for ($i = 0; $i <= $count; $i++) {
                $arr[] = round($quotient * $i) + $paginator->firstPage;
            }
            sort($arr);
            $steps = array_values(array_unique($arr));
        }
        $this->getComponent('paginator')->getTemplate()->steps = $steps;

        parent::render();
    }

    /*     * ******************************
     * Search
     * ****************************** */

    public function isSearchable(): bool {
        return $this->dataSource instanceof SearchableDataSource;
    }

    /**
     * @return FormControl
     * @throws BadRequestException
     */
    protected function createComponentSearchForm(): FormControl {
        if (!$this->isSearchable()) {
            throw new InvalidStateException("Cannot create search form without searchable data source.");
        }
        $control = new FormControl();
        $form = $control->getForm();
        //$form = new Form();
        $form->setMethod(Form::GET);
        $form->addText('term')->setDefaultValue($this->searchTerm)->setAttribute('placeholder', _('Vyhledat'));
        $form->addSubmit('submit', _('Search'));
        $form->onSuccess[] = function (Form $form) {
            $values = $form->getValues();
            $this->searchTerm = $values['term'];
            $this->dataSource->applyFilter($values['term']);
            // TODO is this vv needed? vv
            $count = $this->dataSource->getCount();
            $this->getPaginator()->itemCount = $count;
        };
        return $control;
    }

    /*     * ***************************
     * Apperance
     * *************************** */

    /**
     * Adds button with Bootstrap CSS classes (default is 'default').
     * @param string $name
     * @param string $label
     * @return Button
     * @throws DuplicateButtonException
     */
    protected function addButton($name, $label = null): Button {
        $button = parent::addButton($name, $label);
        $button->setClass('btn btn-sm btn-secondary');
        return $button;
    }

    /**
     * @param $name
     * @param null $label
     * @return GlobalButton
     * @throws DuplicateGlobalButtonException
     */
    public function addGlobalButton($name, $label = null): GlobalButton {
        $button = parent::addGlobalButton($name, $label);
        $button->setClass('btn btn-sm btn-primary');
        return $button;
    }

    /**
     * @param string $field
     * @return void
     * @throws BadTypeException
     * @throws DuplicateColumnException
     * @throws NotImplementedException
     */
    private function addReflectionColumn(string $field) {
        $modelClassName = $this->getModelClassName();
        $factory = $this->tableReflectionFactory->loadRowFactory($field);

        $this->addColumn(str_replace('.', '__', $field), $factory->getTitle())->setRenderer(function ($model) use ($factory, $modelClassName) {
            if (!$model instanceof $modelClassName) {
                $model = $modelClassName::createFromActiveRow($model);
            }
            return $factory->renderValue($model, 1);
        })->setSortable(false);
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     * @param callable $accessCallback ActiveRow=>AbstractModelSingle
     * @throws DuplicateColumnException
     * @throws Exception
     * @deprecated this functionality is moved to getModel in DBReflection AbstractRow
     */
    protected function addJoinedColumn(string $tableName, string $fieldName, callable $accessCallback) {
        $factory = $this->tableReflectionFactory->loadRowFactory($tableName . '.' . $fieldName);
        $this->addColumn($fieldName, $factory->getTitle())->setRenderer(function ($row) use ($factory, $fieldName, $accessCallback) {
            $model = $accessCallback($row);
            return $factory->renderValue($model, 1);
        });
    }

    /**
     * @return string|AbstractModelSingle
     * @throws NotImplementedException
     */
    protected function getModelClassName(): string {
        throw new NotImplementedException();
    }

    /**
     * @param array $fields
     * @return void
     * @throws BadTypeException
     * @throws DuplicateColumnException
     * @throws NotImplementedException
     */
    protected function addColumns(array $fields) {
        foreach ($fields as $name) {
            $this->addReflectionColumn($name);
        }
    }

    /**
     * @param string $destination
     * @param string $id
     * @param string $label
     * @param bool $checkACL
     * @param array $params
     * @return Button
     * @throws DuplicateButtonException
     * @throws NotImplementedException
     */
    protected function addLinkButton(string $destination, string $id, string $label, bool $checkACL = true, array $params = []): Button {
        $modelClassName = $this->getModelClassName();
        $paramMapCallback = function ($model) use ($params): array {
            $hrefParams = [];
            foreach ($params as $key => $value) {
                $hrefParams[$key] = $model->{$value};
            }
            return $hrefParams;
        };
        /** @var Button $button */
        $button = $this->addButton($id, $label)
            ->setText($label)
            ->setLink(function ($model) use ($modelClassName, $destination, $paramMapCallback) {
                if (!$model instanceof $modelClassName) {
                    $model = $modelClassName::createFromActiveRow($model);
                }
                return $this->getPresenter()->link($destination, $paramMapCallback($model));
            });
        if ($checkACL) {
            $button->setShow(function ($model) use ($modelClassName, $destination, $paramMapCallback) {
                if (!$model instanceof $modelClassName) {
                    $model = $modelClassName::createFromActiveRow($model);
                }
                return $this->getPresenter()->authorized($destination, $paramMapCallback($model));
            });
        }
        return $button;
    }

    /**
     * @param string $linkId
     * @param bool $checkACL
     * @return Button
     * @throws DuplicateButtonException
     * @throws Exception
     */
    protected function addLink(string $linkId, bool $checkACL = false): Button {
        $modelClassName = $this->getModelClassName();
        $factory = $this->tableReflectionFactory->loadLinkFactory($linkId);
        $factory->setComponent($this);
        /** @var Button $button */
        $button = $this->addButton(str_replace('.', '_', $linkId), $factory->getText())
            ->setText($factory->getText())
            ->setLink(function ($model) use ($modelClassName, $factory) {
                if (!$model instanceof $modelClassName) {
                    $model = $modelClassName::createFromActiveRow($model);
                }
                return $this->getPresenter()->link($factory->getDestination($model), $factory->prepareParams($model));
            });
        if ($checkACL) {
            $button->setShow(function ($model) use ($modelClassName, $factory) {
                if (!$model instanceof $modelClassName) {
                    $model = $modelClassName::createFromActiveRow($model);
                }
                return $this->getPresenter()->authorized($factory->getDestination($model), $factory->prepareParams($model));
            });
        }
        return $button;
    }

    /**
     * @return GlobalButton|Button
     * @throws DuplicateGlobalButtonException
     * @throws InvalidLinkException
     */
    protected function addCSVDownloadButton(): GlobalButton {
        return $this->addGlobalButton('csv')
            ->setLabel(_('Download as csv'))
            ->setLink($this->link('csv!'));
    }

    /**
     * @throws AbortException
     */
    public function handleCsv() {
        $columns = $this['columns']->components;
        $rows = $this->dataSource->getData();
        $data = [];
        foreach ($rows as $row) {
            $datum = [];
            /** @var Column $column */
            foreach ($columns as $column) {
                $item = $column->prepareValue($row);
                if ($item instanceof Html) {
                    $item = $item->getText();
                }
                $datum[$column->name] = $item;
            }
            $data[] = $datum;
        }
        $response = new CSVResponse($data, 'test.csv');
        $response->setAddHeading(true);
        $response->setQuotes(true);
        $response->setGlue(',');
        $this->getPresenter()->sendResponse($response);
    }
}
