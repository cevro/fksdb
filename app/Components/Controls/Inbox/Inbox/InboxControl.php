<?php

namespace FKSDB\Components\Controls\Inbox\Inbox;

use FKSDB\Components\Controls\Inbox\SeriesTableFormControl;
use FKSDB\Components\Forms\OptimisticForm;
use FKSDB\Model\Logging\ILogger;
use FKSDB\Model\ORM\Models\ModelSubmit;
use FKSDB\Model\ORM\Services\ServiceSubmit;
use FKSDB\Model\Submits\SeriesTable;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\DI\Container;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class InboxControl extends SeriesTableFormControl {

    private ServiceSubmit $serviceSubmit;

    public function __construct(Container $context, SeriesTable $seriesTable) {
        parent::__construct($context, $seriesTable, true);
    }

    final public function injectServiceSubmit(ServiceSubmit $serviceSubmit): void {
        $this->serviceSubmit = $serviceSubmit;
    }

    /**
     * @param Form $form
     * @throws AbortException
     * @throws ForbiddenRequestException
     */
    protected function handleFormSuccess(Form $form): void {
        foreach ($form->getHttpData()['submits'] as $ctId => $tasks) {
            foreach ($tasks as $taskNo => $submittedOn) {
                if (!$this->getSeriesTable()->getContestants()->where('ct_id', $ctId)->fetch()) {
                    // secure check for rewrite ct_id.
                    throw new ForbiddenRequestException();
                }
                $submit = $this->serviceSubmit->findByContestant($ctId, $taskNo);
                if ($submittedOn && $submit) {
                    //   $serviceSubmit->updateModel2($submit, ['submitted_on' => $submittedOn]);
                    //    $this->flashMessage(sprintf(_('Submit #%d updated'), $submit->submit_id), ILogger::INFO);
                } elseif (!$submittedOn && $submit) {
                    $this->flashMessage(\sprintf(_('Submit #%d deleted'), $submit->submit_id), ILogger::WARNING);
                    $submit->delete();
                } elseif ($submittedOn && !$submit) {
                    $this->serviceSubmit->createNewModel([
                        'task_id' => $taskNo,
                        'ct_id' => $ctId,
                        'submitted_on' => $submittedOn,
                        'source' => ModelSubmit::SOURCE_POST,
                    ]);
                    $this->flashMessage(\sprintf(_('Submit for contestant #%d and task %d created'), $ctId, $taskNo), ILogger::SUCCESS);
                } else {
                    // do nothing
                }
            }
        }
        $this->redrawControl();
        $this->getPresenter()->redirect('this');
    }

    public function render(): void {
        $form = $this->getComponent('form');
        if ($form instanceof OptimisticForm) {
            $form->setDefaults();
        }
        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'layout.latte');
        $this->template->render();
    }
}
