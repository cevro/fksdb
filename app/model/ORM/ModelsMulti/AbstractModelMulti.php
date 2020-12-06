<?php

namespace FKSDB\ORM\ModelsMulti;

use FKSDB\ORM\IModel;
use FKSDB\ORM\Models\AbstractModelSingle;
use FKSDB\ORM\ServicesMulti\AbstractServiceMulti;
use LogicException;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\InvalidStateException;
use Nette\SmartObject;

/**
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
abstract class AbstractModelMulti extends ActiveRow implements IModel {
    use SmartObject;

    protected AbstractModelSingle $mainModel;
    protected AbstractModelSingle $joinedModel;
    protected AbstractServiceMulti $service;

    /**
     * @note DO NOT use directly, use AbstractServiceMulti::composeModel or FKSDB\ORM\AbstractModelMulti::createFromExistingModels.
     *
     * @param AbstractServiceMulti|null $service
     * @param AbstractModelSingle $mainModel
     * @param AbstractModelSingle $joinedModel
     */
    public function __construct(?AbstractServiceMulti $service, AbstractModelSingle $mainModel, AbstractModelSingle $joinedModel) {
        parent::__construct($mainModel->toArray(), $mainModel->getTable());
        if (is_null($service)) {
            $this->joinedModel = $joinedModel;
            $this->mainModel = $mainModel;
        } else {
            $this->service = $service;
            $this->setJoinedModel($joinedModel);
            $this->setMainModel($mainModel);
        }
    }

    public static function createFromExistingModels(AbstractModelSingle $mainModel, AbstractModelSingle $joinedModel): self {
        return new static(null, $mainModel, $joinedModel);
    }

    public function toArray(): array {
        return $this->getMainModel()->toArray() + $this->getJoinedModel()->toArray();
    }

    public function getMainModel(): AbstractModelSingle {
        return $this->mainModel;
    }

    public function setMainModel(AbstractModelSingle $mainModel): void {
        if (!isset($this->service)) {
            throw new InvalidStateException('Cannot set main model on multiModel w/out service.');
        }
        $this->mainModel = $mainModel;
        if (!$mainModel->isNew() && $this->getJoinedModel()) { // bind via foreign key
            $joiningColumn = $this->service->getJoiningColumn();
            $this->getJoinedModel()->{$joiningColumn} = $mainModel->getPrimary();
        }
    }

    public function getJoinedModel(): AbstractModelSingle {
        return $this->joinedModel;
    }

    public function setJoinedModel(AbstractModelSingle $joinedModel): void {
        $this->joinedModel = $joinedModel;
    }

    public function getService(): AbstractServiceMulti {
        return $this->service;
    }

    public function setService(AbstractServiceMulti $service): void {
        $this->service = $service;
    }

    /**
     * @param string|int $key
     * @return bool|mixed|ActiveRow|Selection|null
     */
    public function &__get($key) {
        // $value = $this->getMainModel()->{$name} ?? $this->getJoinedModel()->{$name} ?? null;
        if ($this->getMainModel()->__isset($key)) {
            return $this->getMainModel()->__get($key);
        }
        if ($this->getJoinedModel()->__isset($key)) {
            return $this->getJoinedModel()->__get($key);
        }
        // this reference isn't that important
        $null = null;
        return $null;
    }

    /**
     * @param string|int $key
     * @return bool
     */
    public function __isset($key): bool {
        return $this->getMainModel()->__isset($key) || $this->getJoinedModel()->__isset($key);
    }

    /**
     * @param string|int $name
     * @param mixed $value
     */
    public function __set($name, $value): void {
        throw new LogicException("Cannot update multiModel directly.");
    }

    /**
     * @param string|int $name
     */
    public function __unset($name) {
        throw new LogicException("Cannot update multiModel directly.");
    }

    /**
     * @param bool $need
     * @return mixed
     */
    public function getPrimary($need = true) {
        return $this->getJoinedModel()->getPrimary($need);
    }

    public function getSignature(bool $need = true): string {
        return implode('|', (array)$this->getPrimary($need));
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isNew(): bool {
        return $this->getJoinedModel()->isNew();
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool {
        return $this->__isset($offset);
    }

    /**
     * @param mixed $offset
     * @return bool|mixed|ActiveRow|Selection|null
     */
    public function &offsetGet($offset) {
        return $this->__get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void {
        throw new LogicException("Cannot update multiModel directly.");
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void {
        throw new LogicException("Cannot update multiModel directly.");
    }

}
