<?php

namespace FKSDB\Components\Grids\Fyziklani;

use FKSDB\NotImplementedException;
use FKSDB\ORM\DbNames;
use FKSDB\ORM\Models\Fyziklani\ModelFyziklaniTeam;
use FyziklaniModule\BasePresenter;
use Nette\DI\Container;
use NiftyGrid\DataSource\NDataSource;
use NiftyGrid\DuplicateButtonException;
use NiftyGrid\DuplicateColumnException;

/**
 * Class TeamSubmitsGrid
 * @package FKSDB\Components\Grids\Fyziklani
 */
class TeamSubmitsGrid extends SubmitsGrid {

    /**
     * @var ModelFyziklaniTeam
     */
    private $team;

    /**
     * FyziklaniSubmitsGrid constructor.
     * @param ModelFyziklaniTeam $team
     * @param Container $container
     */
    public function __construct(ModelFyziklaniTeam $team, Container $container) {
        $this->team = $team;
        parent::__construct($container);
    }

    /**
     * @param BasePresenter $presenter
     * @throws DuplicateColumnException
     * @throws DuplicateButtonException
     * @throws NotImplementedException
     * @throws NotImplementedException
     * @throws NotImplementedException
     * @throws NotImplementedException
     */
    protected function configure($presenter) {
        parent::configure($presenter);
        $this->paginate = false;
        $this->addColumnTask();

        $this->addColumns([
            DbNames::TAB_FYZIKLANI_SUBMIT . '.points',
            DbNames::TAB_FYZIKLANI_SUBMIT . '.created',
            DbNames::TAB_FYZIKLANI_SUBMIT . '.state',
        ]);
        $this->addLinkButton( ':Fyziklani:Submit:edit', 'edit', _('Edit'), false, ['id' => 'fyziklani_submit_id']);
        $this->addLinkButton( ':Fyziklani:Submit:detail', 'detail', _('Detail'), false, ['id' => 'fyziklani_submit_id']);

        $submits = $this->team->getAllSubmits()
            ->order('fyziklani_submit.created');

        $dataSource = new NDataSource($submits);

        $this->setDataSource($dataSource);
    }
}
