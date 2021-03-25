<?php

namespace FKSDB\Models\ORM\ServicesMulti;

use Fykosak\NetteORM\Exceptions\ModelException;
use FKSDB\Models\ORM\ModelsMulti\AbstractModelMulti;
use Fykosak\NetteORM\AbstractModel;
use FKSDB\Models\ORM\IService;
use FKSDB\Models\ORM\Services\OldAbstractServiceSingle;
use FKSDB\Models\ORM\Tables\MultiTableSelection;
use InvalidArgumentException;
use Nette\Database\Table\ActiveRow;
use Nette\SmartObject;

/**
 * Service for object representing one side of M:N relation, or entity in is-a relation ship.
 * Joined side is in a sense primary (search, select, delete).
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 * @deprecated
 */
abstract class AbstractServiceMulti implements IService {

    use SmartObject;

    public OldAbstractServiceSingle $mainService;
    public OldAbstractServiceSingle $joinedService;
    public string $joiningColumn;
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
        $mainModel = $this->mainService->createNew($data);
        $joinedModel = $this->joinedService->createNew($data);
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
        $mainModel = $this->mainService->createNewModel($data);
        $data[$this->joiningColumn] = $mainModel->{$this->joiningColumn};
        $joinedModel = $this->joinedService->createNewModel($data);
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
    public function updateModel(ActiveRow $model, iterable $data, bool $alive = true): void {
        $this->checkType($model);
        $this->mainService->updateModel($model->mainModel, $data, $alive);
        $this->joinedService->updateModel($model->joinedModel, $data, $alive);
    }

    /**
     * @param ActiveRow|AbstractModelMulti $model
     * @param array $data
     * @return bool
     * @throws ModelException
     */
    public function updateModel2(ActiveRow $model, array $data): bool {
        $this->checkType($model);
        $this->mainService->updateModel2($model->mainModel, $data);
        return $this->joinedService->updateModel2($model->joinedModel, $data);
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

        $mainModel = $model->mainModel;
        $joinedModel = $model->joinedModel;
        $this->mainService->save($mainModel);
        //update ID when it was new
        $model->setMainModel($mainModel, $this);
        $this->joinedService->save($joinedModel);
        $model->joinedModel = $joinedModel;
    }

    /**
     * Use this method to delete a model!
     *
     * @param ActiveRow|AbstractModelMulti $model
     * @throws InvalidArgumentException
     */
    public function dispose(AbstractModelMulti $model): void {
        $this->checkType($model);
        $this->joinedService->dispose($model->joinedModel);
        //TODO here should be deletion of mainModel as well, consider parametrizing this
    }

    /**
     *
     * @param mixed $key ID of the joined models
     * @return AbstractModelMulti|null
     */
    public function findByPrimary($key): ?AbstractModelMulti {
        $joinedModel = $this->joinedService->findByPrimary($key);
        if (!$joinedModel) {
            return null;
        }
        /** @var AbstractModel $mainModel */
        $mainModel = $this->mainService
            ->getTable()
            ->where($this->joiningColumn, $joinedModel->{$this->joiningColumn})
            ->fetch(); //?? is this always unique??
        return $this->composeModel($mainModel, $joinedModel);
    }

    public function getTable(): MultiTableSelection {
        $joinedTable = $this->joinedService->getTable()->getName();
        $mainTable = $this->mainService->getTable()->getName();

        $selection = new MultiTableSelection($this, $joinedTable, $this->joinedService->explorer, $this->joinedService->explorer->getConventions());
        $selection->select("$joinedTable.*");
        $selection->select("$mainTable.*");

        return $selection;
    }

    final public function getModelClassName(): string {
        return $this->modelClassName;
    }
}
