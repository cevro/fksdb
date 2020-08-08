<?php

namespace FKSDB\Components\Controls\Stalking\StalkingComponent;

use FKSDB\Components\Controls\Stalking\StalkingControl;
use FKSDB\Components\Controls\Stalking\StalkingService;
use FKSDB\ORM\AbstractModelSingle;
use FKSDB\ORM\Models\ModelPerson;
use FKSDB\Exceptions\NotImplementedException;
use Nette\InvalidStateException;

/**
 * Class StalkingComponent
 * @author Michal Červeňák <miso@fykos.cz>
 */
class StalkingComponent extends StalkingControl {
    private StalkingService $stalkingService;

    public function injectStalkingService(StalkingService $stalkingService): void {
        $this->stalkingService = $stalkingService;
    }

    /**
     * @param string $section
     * @param ModelPerson $person
     * @param int $userPermission
     * @return void
     * @throws NotImplementedException
     */
    public function render(string $section, ModelPerson $person, int $userPermission) {
        $definition = $this->stalkingService->getSection($section);
        $this->beforeRender($person, _($definition['label']), $userPermission, $definition['minimalPermission']);
        $this->template->userPermission = $userPermission;
        switch ($definition['layout']) {
            case 'single':
                $this->renderSingle($definition, $person);
                return;
            case 'multi':
                $this->renderMulti($definition, $person);
                return;
            default:
                throw new InvalidStateException();
        }
    }

    /**
     * @param array $definition
     * @param ModelPerson $person
     * @return void
     * @throws NotImplementedException
     */
    private function renderSingle(array $definition, ModelPerson $person) {

        $model = null;
        switch ($definition['table']) {
            case 'person_info':
                $model = $person->getInfo();
                break;
            case 'person':
                $model = $person;
                break;
            case 'login':
                $model = $person->getLogin();
                break;
            default:
                throw new NotImplementedException();
        }

        $this->template->model = $model;
        $this->template->rows = $definition['rows'];
        $this->template->setFile(__DIR__ . '/layout.single.latte');
        $this->template->render();
    }

    /**
     * @param mixed[]|AbstractModelSingle[] $definition
     * @param ModelPerson $person
     * @return void
     */
    private function renderMulti(array $definition, ModelPerson $person) {
        $models = [];
        $query = $person->related($definition['table']);
        foreach ($query as $datum) {
            $models[] = ($definition['model'])::createFromActiveRow($datum);
        }
        $this->template->links = $definition['links'];
        $this->template->rows = $definition['rows'];
        $this->template->models = $models;
        $this->template->itemHeadline = $definition['itemHeadline'];
        $this->template->setFile(__DIR__ . '/layout.multi.latte');
        $this->template->render();
    }
}
