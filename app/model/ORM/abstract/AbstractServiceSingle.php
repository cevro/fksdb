<?php

namespace FKSDB\ORM;

use FKSDB\ORM\Tables\TypedTableSelection;
use InvalidArgumentException;
use ModelException;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\IConventions;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection as TableSelection;
use Nette\InvalidStateException;
use PDOException;
use Traversable;

/**
 * Service class to high-level manipulation with ORM objects.
 * Use singleton descedants implemetations.
 *
 * @note Because of compatibility with PHP 5.2 (no LSB), part of the code has to be
 *       duplicated in all descedant classes.
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
abstract class AbstractServiceSingle extends TableSelection implements IService {

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * AbstractServiceSingle constructor.
     * @param Context $connection
     * @param IConventions $conventions
     * FKSDB\ORM\AbstractServiceSingle constructor.
     */
    public function __construct(Context $connection, IConventions $conventions) {
        parent::__construct($connection, $conventions, $this->getTableName());
    }

    /**
     * @param Traversable|array|null $data
     * @return AbstractModelSingle
     */
    public function createNewModel($data = null): AbstractModelSingle {
        $modelClassName = $this->getModelClassName();
        $data = $this->filterData($data);
        try {
            $result = $this->getTable()->insert($data);
            if ($result !== false) {
                /**
                 * @var AbstractModelSingle $model
                 */
                $model = ($modelClassName)::createFromActiveRow($result);
                $model->setNew(false); // only for old compatibility
                return $model;
            }
        } catch (PDOException $exception) {
            throw new ModelException('Error when storing model.', null, $exception);
        }
        $code = $this->getConnection()->getPdo()->errorCode();
        throw new ModelException("$code: Error when storing a model.");
    }


    /**
     * Use this method to create new models!
     *
     * @param Traversable $data
     * @return AbstractModelSingle
     * @throws ModelException
     * @deprecated use createNewModel
     */
    public function createNew($data = null) {
        if ($data === null) {
            $data = $this->getDefaultData();
        }
        $result = $this->createFromArray((array)$data);
        $result->setNew();
        return $result;
    }

    /**
     * @internal Used also in MultiTableSelection.
     *
     * @param array $data
     * @return AbstractModelSingle
     * @deprecated
     */
    public function createFromArray(array $data) {
        $className = $this->getModelClassName();
        $data = $this->filterData($data);
        $result = new $className($data, $this);
        return $result;
    }

    /**
     * Syntactic sugar.
     *
     * @param int $key
     * @return ActiveRow|null
     */
    public function findByPrimary($key) {
        $result = $this->getTable()->get($key);
        if ($result !== false) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * @param int $key
     * @return AbstractModelSingle|null
     */
    public function findByPrimary2(int $key) {
        $result = $this->getTable()->get($key);
        if ($result !== false) {
            return $this->getModelClassName()::createFromActiveRow($result);
        } else {
            return null;
        }
    }

    /**
     * Updates values in model from given data.
     *
     * @param IModel $model
     * @param array $data
     * @param boolean $alive
     * @deprecated
     */
    public function updateModel(IModel $model, $data, $alive = true) {
        $modelClassName = $this->getModelClassName();
        if (!$model instanceof $modelClassName) {
            throw new InvalidArgumentException('Service for class ' . $this->getModelClassName() . ' cannot store ' . get_class($model));
        }

        $data = $this->filterData($data);
        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }
    }

    /**
     * @param AbstractModelSingle|IModel $model
     * @param Traversable|array $data
     * @return int
     * @throws InvalidArgumentException
     */
    public function updateModel2(IModel $model, $data) {
        $this->checkType($model);
        $data = $this->filterData($data);
        return $model->update($data);
    }

    /**
     * @param AbstractModelSingle $model
     * @return AbstractModelSingle|null
     */
    public function refresh(AbstractModelSingle $model) {
        return $this->findByPrimary2($model->getPrimary(true));
    }

    /**
     * Use this method to store a model!
     *
     * @param IModel|AbstractModelSingle $model
     * @throws InvalidArgumentException
     * @throws ModelException
     * @deprecated
     */
    public function save(IModel & $model) {
        $modelClassName = $this->getModelClassName();
        if (!$model instanceof $modelClassName) {
            throw new InvalidArgumentException('Service for class ' . $this->getModelClassName() . ' cannot store ' . get_class($model));
        }
        try {
            if ($model->isNew()) {
                $result = $this->getTable()->insert($model->toArray());
                if ($result !== false) {
                    $model = $modelClassName::createFromTableRow($result);
                    $model->setNew(false);
                } else {
                    $result = false;
                }
            } else {
                $result = $model->update() !== false;
            }
        } catch (PDOException $exception) {
            throw new ModelException('Error when storing model.', null, $exception);
        }
        if (!$result) {
            $code = $this->context->getConnection()->getPdo()->errorCode();
            throw new ModelException("$code: Error when storing a model.");
        }
    }

    /**
     * Use this method to delete a model!
     * (Name chosen not to collide with parent.)
     *
     * @param IModel|AbstractModelSingle $model
     * @throws InvalidArgumentException
     * @throws InvalidStateException
     */
    public function dispose(IModel $model) {
        $this->checkType($model);
        if (!$model->isNew() && $model->delete() === false) {
            $code = $this->context->getConnection()->getPdo()->errorCode();
            throw new ModelException("$code: Error when deleting a model.");
        }
    }

    /**
     * @return TableSelection
     */
    public function getTable() {
        return new TypedTableSelection($this->getModelClassName(), $this->getTableName(), $this->context, $this->conventions);
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection {
        return $this->context->getConnection();
    }

    /**
     * @return Context
     */
    public function getContext(): Context {
        return $this->context;
    }

    /**
     * @return IConventions
     */
    public function getConventions(): IConventions {
        return $this->conventions;
    }

    /**
     * @param AbstractModelSingle|IModel $model
     * @throws InvalidArgumentException
     */
    private function checkType(AbstractModelSingle $model) {
        $modelClassName = $this->getModelClassName();
        if (!$model instanceof $modelClassName) {
            throw new InvalidArgumentException('Service for class ' . $this->getModelClassName() . ' cannot store ' . get_class($model));
        }
    }

    protected $defaults = null;

    /**
     * Default data for the new model.
     * TODO is this really needed?
     * @return array
     */
    protected function getDefaultData() {
        if ($this->defaults == null) {
            $this->defaults = [];
            foreach ($this->getColumnMetadata() as $column) {
                if ($column['nativetype'] == 'TIMESTAMP' && isset($column['default']) && $column['default'] == 'CURRENT_TIMESTAMP') {
                    continue;
                }
                $this->defaults[$column['name']] = isset($column['default']) ? $column['default'] : null;
            }
        }
        return $this->defaults;
    }

    /**
     * Omits array elements whose keys aren't columns in the table.
     *
     * @param array|Traversable|null $data
     * @return array|null
     */
    protected function filterData($data) {
        if ($data === null) {
            return null;
        }
        $result = [];
        foreach ($this->getColumnMetadata() as $column) {
            $name = $column['name'];
            if (array_key_exists($name, $data)) {
                $result[$name] = $data[$name];
            }
        }
        return $result;
    }

    private $columns;

    /**
     * @return array
     */
    private function getColumnMetadata() {
        if ($this->columns === null) {
            $this->columns = $this->context->getConnection()->getSupplementalDriver()->getColumns($this->getTableName());
        }
        return $this->columns;
    }

    /**
     * @return string|AbstractModelSingle
     */
    abstract protected function getModelClassName(): string;

    /**
     * @return string
     */
    abstract protected function getTableName(): string;

}

