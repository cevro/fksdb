<?php

namespace FKSDB\Components\Controls\Entity\Org;

use FKSDB\Components\Controls\Entity\AbstractEntityFormComponent;
use FKSDB\Components\Controls\Entity\IEditEntityForm;
use FKSDB\Components\Controls\Entity\ReferencedPersonTrait;
use FKSDB\DBReflection\ColumnFactories\AbstractColumnException;
use FKSDB\DBReflection\OmittedControlException;
use FKSDB\Components\Forms\Containers\ModelContainer;
use FKSDB\Components\Forms\Factories\SingleReflectionFormFactory;
use FKSDB\Exceptions\BadTypeException;
use FKSDB\Messages\Message;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\Models\ModelContest;
use FKSDB\ORM\Models\ModelOrg;
use FKSDB\ORM\Services\ServiceOrg;
use FKSDB\Utils\FormUtils;
use FKSDB\YearCalculator;
use Nette\Application\AbortException;
use Nette\Forms\Form;
use Nette\DI\Container;

/**
 * Class OrgForm
 * @author Michal Červeňák <miso@fykos.cz>
 */
class OrgFormComponent extends AbstractEntityFormComponent implements IEditEntityForm {
    use ReferencedPersonTrait;

    const CONTAINER = 'org';

    protected ServiceOrg $serviceOrg;

    protected ModelContest $contest;

    /** @var ModelOrg */
    private $model;

    private SingleReflectionFormFactory $singleReflectionFormFactory;

    private YearCalculator $yearCalculator;

    /**
     * AbstractForm constructor.
     * @param Container $container
     * @param ModelContest $contest
     * @param bool $create
     */
    public function __construct(Container $container, ModelContest $contest, bool $create) {
        parent::__construct($container, $create);
        $this->contest = $contest;
    }

    public function injectPrimary(SingleReflectionFormFactory $singleReflectionFormFactory, ServiceOrg $serviceOrg, YearCalculator $yearCalculator): void {
        $this->singleReflectionFormFactory = $singleReflectionFormFactory;
        $this->serviceOrg = $serviceOrg;
        $this->yearCalculator = $yearCalculator;
    }

    /**
     * @param Form $form
     * @return void
     * @throws AbstractColumnException
     * @throws BadTypeException
     * @throws OmittedControlException
     */
    protected function configureForm(Form $form) {
        $container = $this->createOrgContainer();
        $personInput = $this->createPersonSelect();
        if (!$this->create) {
            $personInput->setDisabled(true);
        }
        $container->addComponent($personInput, 'person_id', 'since');
        $form->addComponent($container, self::CONTAINER);
    }

    /**
     * @param Form $form
     * @return void
     * @throws AbortException
     */
    protected function handleFormSuccess(Form $form) {
        $data = FormUtils::emptyStrToNull($form->getValues()[self::CONTAINER], true);
        if (!isset($data['contest_id'])) {
            $data['contest_id'] = $this->contest->contest_id;
        }
        if ($this->create) {
            $this->getORMService()->createNewModel($data);
        } else {
            $this->getORMService()->updateModel2($this->model, $data);
        }
        $this->getPresenter()->flashMessage($this->create ? _('Org has been created.') : _('Org has been updated.'), Message::LVL_SUCCESS);
        $this->getPresenter()->redirect('list');
    }

    /**
     * @param AbstractModelSingle $model
     * @return void
     * @throws BadTypeException
     */
    public function setModel(AbstractModelSingle $model) {
        $this->model = $model;
        $this->getForm()->setDefaults([self::CONTAINER => $model->toArray()]);
    }

    protected function getORMService(): ServiceOrg {
        return $this->serviceOrg;
    }

    /**
     * @return ModelContainer
     * @throws BadTypeException
     * @throws AbstractColumnException
     * @throws OmittedControlException
     */
    private function createOrgContainer(): ModelContainer {
        $container = new ModelContainer();
        $min = $this->yearCalculator->getFirstYear($this->contest);
        $max = $this->yearCalculator->getLastYear($this->contest);

        foreach (['since', 'until'] as $field) {
            $control = $this->singleReflectionFormFactory->createField('org', $field, $min, $max);
            $container->addComponent($control, $field);
        }

        foreach (['role', 'tex_signature', 'domain_alias', 'order', 'contribution'] as $field) {
            $control = $this->singleReflectionFormFactory->createField('org', $field);
            $container->addComponent($control, $field);
        }
        return $container;
    }
}
