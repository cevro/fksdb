<?php

namespace FKSDB\Models\ORM\Links;

use FKSDB\Models\ORM\ReferencedFactory;
use FKSDB\Models\Entity\CannotAccessModelException;
use FKSDB\Models\Exceptions\BadTypeException;
use FKSDB\Models\ORM\Models\AbstractModelSingle;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

/**
 * Class AbstractLink
 * @author Michal Červeňák <miso@fykos.cz>
 */
abstract class AbstractLink implements ILinkFactory {

    protected ReferencedFactory $referencedFactory;

    public function setReferencedFactory(ReferencedFactory $factory): void {
        $this->referencedFactory = $factory;
    }

    /**
     * @param Presenter $presenter
     * @param AbstractModelSingle $model
     * @return string
     * @throws InvalidLinkException
     * @throws CannotAccessModelException
     */
    public function create(Presenter $presenter, AbstractModelSingle $model): string {
        return $presenter->link(...$this->createLinkParameters($model));
    }

    /**
     * @param AbstractModelSingle $modelSingle
     * @return AbstractModelSingle|null
     * @throws CannotAccessModelException
     */
    protected function getModel(AbstractModelSingle $modelSingle): ?AbstractModelSingle {
        return ReferencedFactory::accessModel($modelSingle, $this->referencedFactory->getModelClassName());
    }

    /**
     * @param AbstractModelSingle $model
     * @return array
     * @throws CannotAccessModelException
     * @throws InvalidLinkException
     */
    public function createLinkParameters(AbstractModelSingle $model): array {
        $model = $this->getModel($model);
        if (is_null($model)) {
            throw new InvalidLinkException();
        }
        return [
            $this->getDestination($model),
            $this->prepareParams($model),
        ];
    }

    abstract protected function getDestination(AbstractModelSingle $model): string;

    abstract protected function prepareParams(AbstractModelSingle $model): array;
}
