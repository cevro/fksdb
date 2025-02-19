<?php

declare(strict_types=1);

namespace FKSDB\Components\Grids\Deduplicate;

use FKSDB\Components\Grids\BaseGrid;
use FKSDB\Models\ORM\Models\ModelPerson;
use FKSDB\Models\Persons\Deduplication\DuplicateFinder;
use Fykosak\NetteORM\TypedTableSelection;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use NiftyGrid\DataSource\IDataSource;
use NiftyGrid\DataSource\NDataSource;
use NiftyGrid\DuplicateButtonException;
use NiftyGrid\DuplicateColumnException;

class PersonsGrid extends BaseGrid
{

    private TypedTableSelection $trunkPersons;

    /** @var ModelPerson[] trunkId => ModelPerson */
    private array $pairs;

    public function __construct(TypedTableSelection $trunkPersons, array $pairs, Container $container)
    {
        parent::__construct($container);
        $this->trunkPersons = $trunkPersons;
        $this->pairs = $pairs;
    }

    protected function getData(): IDataSource
    {
        return new NDataSource($this->trunkPersons);
    }

    /**
     * @throws DuplicateButtonException
     * @throws DuplicateColumnException
     */
    protected function configure(Presenter $presenter): void
    {
        parent::configure($presenter);

        /***** columns ****/

        $this->addColumn('display_name_a', _('Person A'))
            ->setRenderer(
                fn(ModelPerson $row): string => $this->renderPerson($row)
            )
            ->setSortable(false);
        $this->addColumn('display_name_b', _('Person B'))
            ->setRenderer(
                fn(ModelPerson $row): string => $this->renderPerson(
                    $this->pairs[$row->person_id][DuplicateFinder::IDX_PERSON]
                )
            )
            ->setSortable(false);
        $this->addColumn('score', _('Similarity'))
            ->setRenderer(
                fn(ModelPerson $row): string => sprintf(
                    '%0.2f',
                    $this->pairs[$row->person_id][DuplicateFinder::IDX_SCORE]
                )
            )
            ->setSortable(false);

        /**** operations *****/

        $this->addButton('mergeAB', _('Merge A<-B'))
            ->setText(_('Merge A<-B'))
            ->setClass('btn btn-sm btn-primary')
            ->setLink(fn(ModelPerson $row): string => $this->getPresenter()->link('Deduplicate:merge', [
                'trunkId' => $row->person_id,
                'mergedId' => $this->pairs[$row->person_id][DuplicateFinder::IDX_PERSON]->person_id,
            ]))
            ->setShow(fn(ModelPerson $row): bool => $this->getPresenter()->authorized('Deduplicate:merge', [
                'trunkId' => $row->person_id,
                'mergedId' => $this->pairs[$row->person_id][DuplicateFinder::IDX_PERSON]->person_id,
            ]));
        $this->addButton('mergeBA', _('Merge B<-A'))
            ->setText(_('Merge B<-A'))
            ->setLink(fn(ModelPerson $row): string => $this->getPresenter()->link('Deduplicate:merge', [
                'trunkId' => $this->pairs[$row->person_id][DuplicateFinder::IDX_PERSON]->person_id,
                'mergedId' => $row->person_id,
            ]))
            ->setShow(fn(ModelPerson $row): bool => $this->getPresenter()->authorized('Deduplicate:merge', [
                'trunkId' => $this->pairs[$row->person_id][DuplicateFinder::IDX_PERSON]->person_id,
                'mergedId' => $row->person_id,
            ]));
        $this->addButton('dontMerge', _('It\'s not a duplicity'))
            ->setText(_('It\'s not a duplicity'))
            ->setClass('btn btn-sm btn-primary')
            ->setLink(fn(ModelPerson $row): string => $this->getPresenter()->link('Deduplicate:dontMerge', [
                'trunkId' => $this->pairs[$row->person_id][DuplicateFinder::IDX_PERSON]->person_id,
                'mergedId' => $row->person_id,
            ]))
            ->setShow(fn(ModelPerson $row): bool => $this->getPresenter()->authorized('Deduplicate:dontMerge', [
                'trunkId' => $this->pairs[$row->person_id][DuplicateFinder::IDX_PERSON]->person_id,
                'mergedId' => $row->person_id,
            ]));
    }

    private function renderPerson(ModelPerson $person): string
    {
        return $person->getFullName();
        // return (new PersonLink($this->getPresenter()))($person);
    }
}
