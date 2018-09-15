<?php

namespace FKSDB\Components\Forms\Controls;

use FKS\Components\Forms\Controls\ModelDataConflictException;
use FKSDB\Components\Forms\Containers\Models\IReferencedSetter;
use FKSDB\Components\Forms\Containers\Models\ReferencedContainer;
use FKS\Utils\Promise;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\Form;
use Nette\Mail\Message;
use ORM\IModel;
use ORM\IService;

/**
 * Be careful when calling getValue as it executes SQL queries and thus
 * it should always be run inside a transaction.
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ReferencedId extends HiddenField {

    const VALUE_PROMISE = '__promise';

    /**
     * @var ReferencedContainer
     */
    private $referencedContainer;

    /**
     * @var Promise
     */
    private $promise;

    /**
     * @var IService
     */
    private $service;

    /**
     * @var IReferencedHandler
     */
    private $handler;

    /**
     * @var IReferencedSetter
     */
    private $referencedSetter;

    /**
     * @var boolean
     */
    private $modelCreated;

    /**
     * @var IModel
     */
    private $model;

    function __construct(IService $service, IReferencedHandler $handler, IReferencedSetter $referencedSetter) {
        parent::__construct();
        $this->monitor('Nette\Forms\Form');

        $this->service = $service;
        $this->handler = $handler;
        $this->referencedSetter = $referencedSetter;
    }

    public function getReferencedContainer() {
        return $this->referencedContainer;
    }

    public function setReferencedContainer(ReferencedContainer $referencedContainer) {
        $this->referencedContainer = $referencedContainer;
    }

    protected function getPromise() {
        return $this->promise;
    }

    private function setPromise(Promise $promise) {
        $this->promise = $promise;
    }

    public function getService() {
        return $this->service;
    }

    public function getHandler() {
        return $this->handler;
    }

    public function getModelCreated() {
        return $this->modelCreated;
    }

    public function setModelCreated($modelCreated) {
        $this->modelCreated = $modelCreated;
    }

    public function getModel() {
        return $this->model;
    }

    public function setValue($pvalue, $force = false) {
        $isPromise = ($pvalue === self::VALUE_PROMISE);
        if (!($pvalue instanceof IModel) && !$isPromise) {
            $pvalue = $this->service->findByPrimary($pvalue);
        } else if ($isPromise) {
            $pvalue = $this->service->createNew();
        } else if ($pvalue instanceof IModel) {
            $this->model = $pvalue;
        }
        $container = $this->referencedContainer;
        if (!$pvalue) {
            $container->setSearchButton(true);
            $container->setClearButton(false);
        } else {
            $container->setSearchButton(false);
            $container->setClearButton(true);
        }
        $this->referencedSetter->setModel($container, $pvalue, $force);

        if ($isPromise) {
            $value = self::VALUE_PROMISE;
        } else if ($pvalue instanceof IModel) {
            $value = $pvalue->getPrimary();
        } else {
            $value = $pvalue;
        }
        parent::setValue($value);
    }

    /**
     * If you are calling this method out of transaction, set $fullfilPromise to
     * false. This is the case for event form adjustments.
     *
     * @param boolean $fullfilPromise
     * @return mixed
     */
    public function getValue($fullfilPromise = true) {
        if ($fullfilPromise && $this->promise) {
            return $this->promise->getValue();
        }

        $value = parent::getValue();
        return $value ?: null;
    }

    public function rollback() {
        if ($this->getModelCreated()) {
            $this->referencedSetter->setModel($this->referencedContainer, NULL, IReferencedSetter::MODE_ROLLBACK);
            if (parent::getValue()) {
                parent::setValue(self::VALUE_PROMISE);
            }
        }
    }

    public function setDisabled($value = TRUE) {
        $this->referencedContainer->setDisabled($value);
    }

    private function createPromise() {
        $referencedId = $this->getValue();

        $values = $this->referencedContainer->getValues();

        $promise = new Promise(function () use ($referencedId, $values) {
            $messages = [];
            try {
                if ($referencedId === self::VALUE_PROMISE) {
                    $model = $this->handler->createFromValues($values, $messages);

                    $this->addMessages($messages);
                    $this->setValue($model, IReferencedSetter::MODE_FORCE);
                    $this->setModelCreated(true);
                    return $model->getPrimary();
                } else if ($referencedId) {
                    $model = $this->getService()->findByPrimary($referencedId);

                    $this->handler->update($model, $values, $messages);
                    $this->addMessages($messages);
                    // reload the model (this is workaround to avoid caching of empty but newly created referenced/related models)
                    $model = $this->getService()->findByPrimary($model->getPrimary());
                    $this->setValue($model, IReferencedSetter::MODE_FORCE);
                    return $referencedId;
                } else {
                    $this->setValue(null, IReferencedSetter::MODE_FORCE);
                }
            } catch (ModelDataConflictException $e) {
                $e->setReferencedId($this);
                throw $e;
            }

        });
        $this->setValue($referencedId);
        $this->setPromise($promise);
    }

    /**
     * @param $messages Message[]
     */
    private function addMessages(array $messages) {
        /**
         * @var $message Message
         */
        foreach ($messages as $message) {
            $this->getForm()->addError($message->getText());
        }
    }

    private $attachedOnValidate = false;

    protected function attached($obj) {
        parent::attached($obj);
        if (!$this->attachedOnValidate && $obj instanceof Form) {
            $obj->onValidate[] = function () {
                $this->createPromise();
            };
            $this->attachedOnValidate = true;
        }
    }

}
