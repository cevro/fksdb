<?php

namespace FKSDB\Modules\CommonModule;

use FKSDB\Components\Grids\Deduplicate\PersonsGrid;
use FKSDB\ORM\Services\ServicePerson;
use FKSDB\UI\PageTitle;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Database\Table\ActiveRow;
use FKSDB\Persons\Deduplication\DuplicateFinder;
use FKSDB\Persons\Deduplication\Merger;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class DeduplicatePresenter extends BasePresenter {

    private ServicePerson $servicePerson;
    private Merger $merger;

    final public function injectQuarterly(ServicePerson $servicePerson, Merger $merger): void {
        $this->servicePerson = $servicePerson;
        $this->merger = $merger;
    }

    public function authorizedPerson(): void {
        $this->setAuthorized($this->contestAuthorizator->isAllowedForAnyContest('person', 'list'));
    }

    public function titlePerson(): void {
        $this->setPageTitle(new PageTitle(_('Duplicitní osoby'), 'fa fa-exchange'));
    }

    /**
     * @throws ForbiddenRequestException
     * @throws AbortException
     */
    public function handleBatchMerge(): void {
        if (!$this->contestAuthorizator->isAllowedForAnyContest('person', 'merge')) { //TODO generic authorizator
            throw new ForbiddenRequestException();
        }
        //TODO later specialize for each entinty type
        $finder = $this->createPersonDuplicateFinder();
        $pairs = $finder->getPairs();
        $trunkPersons = $this->servicePerson->getTable()->where('person_id', array_keys($pairs));
        $table = $this->servicePerson->getTable()->getName();

        foreach ($pairs as $trunkId => $mergedData) {
            if (!isset($trunkPersons[$trunkId])) {
                continue; // the trunk can be already merged somewhere else as merged
            }
            $trunkRow = $trunkPersons[$trunkId];
            /** @var ActiveRow $mergedRow */
            $mergedRow = $mergedData[DuplicateFinder::IDX_PERSON];
            $this->merger->setMergedPair($trunkRow, $mergedRow);

            if ($this->merger->merge()) {
                $this->flashMessage(sprintf(_('%s (%d) a %s (%d) sloučeny.'), $table, $trunkRow->getPrimary(), $table, $mergedRow->getPrimary()), self::FLASH_SUCCESS);
            } else {
                $this->flashMessage(sprintf(_('%s (%d) a %s (%d) potřebují vyřešit konflitky.'), $table, $trunkRow->getPrimary(), $table, $mergedRow->getPrimary()), self::FLASH_INFO);
            }
        }

        $this->redirect('this');
    }

    protected function createComponentPersonsGrid(): PersonsGrid {
        $duplicateFinder = $this->createPersonDuplicateFinder();
        $pairs = $duplicateFinder->getPairs();
        $trunkPersons = $this->servicePerson->getTable()->where('person_id', array_keys($pairs));

        return new PersonsGrid($trunkPersons, $pairs, $this->getContext());
    }

    protected function createPersonDuplicateFinder(): DuplicateFinder {
        return new DuplicateFinder($this->servicePerson, $this->getContext());
    }
}
