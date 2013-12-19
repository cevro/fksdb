<?php

namespace FKSDB\Components\Grids;

use Authorization\ContestAuthorizator;
use NiftyGrid\DataSource\NDataSource;
use ServiceStoredQuery;

/**
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 */
class StoredQueriesGrid extends BaseGrid {
    /** @const No. of characters that are showed from query description. */
    const DESCRIPTION_TRUNC = 80;

    /**
     * @var ServiceStoredQuery
     */
    private $serviceStoredQuery;

    /**
     * @var ContestAuthorizator
     */
    private $contestAuthorizator;

    function __construct(ServiceStoredQuery $serviceStoredQuery, ContestAuthorizator $contestAuthorizator) {
        $this->serviceStoredQuery = $serviceStoredQuery;
        $this->contestAuthorizator = $contestAuthorizator;
    }

    protected function configure($presenter) {
        parent::configure($presenter);

        //
        // data
        //
        $queries = $this->serviceStoredQuery->getTable();

        $this->setDataSource(new NDataSource($queries));

        //
        // columns
        //
        $this->addColumn('name', _('Název'));
        $this->addColumn('description', _('Popis'))->setTruncate(self::DESCRIPTION_TRUNC);

        //
        // operations
        //
        $that = $this;
        $contest = $presenter->getSelectedContest();
        $this->addButton("edit", _("Upravit"))
                ->setText('Upravit') //todo i18n
                ->setLink(function($row) use ($that) {
                            return $that->getPresenter()->link("edit", $row->query_id);
                        })
                ->setShow(function($row) use ($that, $contest) {
                            return $that->contestAuthorizator->isAllowed($row, 'edit', $contest);
                        });
        $this->addButton("show", _("Podrobnosti"))
                ->setText('Podrobnosti') //todo i18n
                ->setLink(function($row) use ($that) {
                            return $that->getPresenter()->link("show", $row->query_id);
                        })
                ->setShow(function($row) use ($that, $contest) {
                            return $that->contestAuthorizator->isAllowed($row, 'show', $contest);
                        });

        $this->addButton("execute", _("Spustit"))
                ->setClass("btn btn-xs btn-primary")
                ->setText('Spustit') //todo i18n
                ->setLink(function($row) use ($that) {
                            return $that->getPresenter()->link("execute", $row->query_id);
                        })
                ->setShow(function($row) use ($that, $contest) {
                            return $that->contestAuthorizator->isAllowed($row, 'execute', $contest);
                        });

        $this->addGlobalButton('compose', 'Napsat dotaz')
                ->setLink($this->getPresenter()->link('compose'));
    }

}
