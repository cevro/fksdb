<?php

namespace FKSDB\Models\ORM\ServicesMulti;

use Fykosak\NetteORM\Exceptions\ModelException;
use FKSDB\Models\ORM\ModelsMulti\AbstractModelMulti;
use Fykosak\NetteORM\AbstractModel;
use FKSDB\Models\ORM\IService;
use FKSDB\Models\ORM\Services\OldAbstractServiceSingle;
use FKSDB\Models\ORM\Tables\MultiTableSelection;
use InvalidArgumentException;
use Nette\Database\Connection;
use Nette\Database\Conventions;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\SmartObject;

/**
 * Service for object representing one side of M:N relation, or entity in is-a relation ship.
 * Joined side is in a sense primary (search, select, delete).
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
abstract class AbstractServiceMulti implements IService {

    use SmartObject;

    protected OldAbstractServiceSingle $mainService;
    protected OldAbstractServiceSingle $joinedService;
    private string $joiningColumn;
    private string $modelClassName;

    public function __construct(OldAbstractServiceSingle $mainService, OldAbstractServiceSingle $joinedService, string $joiningColumn, string $modelClassName) {
        $this->mainService = $mainService;
        $this->joinedService = $joinedService;
        $this->modelClassName = $modelClassName;
        $this->joiningColumn = $joiningColumn;
    }

    /**
     * Use this method to create new models!
     *
     * @param iterable|null $data
     * @return AbstractModelMulti
     * @throws ModelException
     * @deprecated
     */
    public function createNew(?iterable $data = null): ActiveRow {
        $mainModel = $this->getMainService()->createNew($data);
        $joinedModel = $this->getJoinedService()->createNew($data);
        return $this->composeModel($mainModel, $joinedModel);
    }

    /**
     * Use this method to create new models!
     *
     * @param array $data
     * @return AbstractModelMulti
     * @throws ModelException
     */
    public function createNewModel(array $data): AbstractModelMulti {
        $mainModel = $this->getMainService()->createNewModel($data);
        $data[$this->getJoiningColumn()] = $mainModel->{$this->getJoiningColumn()};
        $joinedModel = $this->getJoinedService()->createNewModel($data);
        return $this->composeModel($mainModel, $joinedModel);
    }

    public function composeModel(AbstractModel $mainModel, AbstractModel $joinedModel): AbstractModelMulti {
        $className = $this->getModelClassName();
        return new $className($this, $mainModel, $joinedModel);
    }

    /**
     * @param ActiveRow|AbstractModelMulti $model
     * @param iterable $data
     * @param bool $alive
     * @return void
     * @deprecated
     */
    public function updateModelLegacy(ActiveRow $model, iterable $data, bool $alive = true): void {
        $this->checkType($model);
        $this->getMainService()->updateModelLegacy($model->getMainModel(), $data, $alive);
        $this->getJoinedService()->updateModelLegacy($model->getJoinedModel(), $data, $alive);
    }

    /**
     * @param ActiveRow|AbstractModelMulti $model
     * @param array $data
     * @return bool
     * @throws ModelException
     */
    public function updateModel(ActiveRow $model, array $data): bool {
        $this->checkType($model);
        $this->getMainService()->updateModel($model->getMainModel(), $data);
        return $this->getJoinedService()->updateModel($model->getJoinedModel(), $data);
    }

    /**
     * @param AbstractModelMulti|ActiveRow $model
     * @throws InvalidArgumentException
     */
    private function checkType(AbstractModelMulti $model): void {
        $modelClassName = $this->getModelClassName();
        if (!$model instanceof $modelClassName) {
            throw new InvalidArgumentException('Service for class ' . $this->getModelClassName() . ' cannot store ' . get_class($model));
        }
    }

    /**
     * Use this method to store a model!
     *
     * @param ActiveRow|AbstractModelMulti $model
     * @throws ModelException
     * @deprecated
     */
    public function save(ActiveRow &$model): void {
        $this->checkType($model);

        $mainModel = $model->getMainModel();
        $joinedModel = $model->getJoinedModel();
        $this->getMainService()->save($mainModel);
        //update ID when it was new
        $model->setService($this);
        $model->setMainModel($mainModel);
        $this->getJoinedService()->save($joinedModel);
        $model->setJoinedModel($joinedModel);
    }

    /**
     * Use this method to delete a model!
     *
     * @param ActiveRow|AbstractModelMulti $model
     * @throws InvalidArgumentException
     */
    public function dispose(AbstractModelMulti $model): void {
        $this->checkType($model);
        $this->getJoinedService()->dispose($model->getJoinedModel());
        //TODO here should be deletion of mainModel as well, consider parametrizing this
    }

    final public function getMainService(): OldAbstractServiceSingle {
        return $this->mainService;
    }

    final public function getJoinedService(): OldAbstractServiceSingle {
        return $this->joinedService;
    }

    public function getConnection(): Connection {
        return $this->mainService->explorer->getConnection();
    }

    public function getContext(): Explorer {
        return $this->mainService->explorer;
    }
    public function getExplorer(): Explorer {
        return $this->mainService->explorer;
    }

    public function getConventions(): Conventions {
        return $this->mainService->explorer->getConventions();
    }

    /**
     *
     * @param mixed $key ID of the joined models
     * @return AbstractModelMulti|null
     */
    public function findByPrimary($key): ?AbstractModelMulti {
        $joinedModel = $this->getJoinedService()->findByPrimary($key);
        if (!$joinedModel) {
            return null;
        }
        /** @var AbstractModel $mainModel */
        $mainModel = $this->getMainService()
            ->getTable()
            ->where($this->getJoiningColumn(), $joinedModel->{$this->getJoiningColumn()})
            ->fetch(); //?? is this always unique??
        return $this->composeModel($mainModel, $joinedModel);
    }

    public function getTable(): MultiTableSelection {
        $joinedTable = $this->getJoinedService()->getTable()->getName();
        $mainTable = $this->getMainService()->getTable()->getName();

        $selection = new MultiTableSelection($this, $joinedTable, $this->getJoinedService()->explorer, $this->getJoinedService()->explorer->getConventions());
        $selection->select("$joinedTable.*");
        $selection->select("$mainTable.*");

        return $selection;
    }

    final public function getJoiningColumn(): string {
        return $this->joiningColumn;
    }

    final public function getModelClassName(): string {
        return $this->modelClassName;
    }
}
