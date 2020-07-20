<?php

namespace FKSDB\Fyziklani\Submit;

use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Services\Fyziklani\ServiceFyziklaniSubmit;
use FKSDB\ORM\Services\Fyziklani\ServiceFyziklaniTask;
use FKSDB\ORM\Services\Fyziklani\ServiceFyziklaniTeam;
use Nette\Security\User;

/**
 * Class HandlerFactory
 * @author Michal Červeňák <miso@fykos.cz>
 */
class HandlerFactory {

    /** @var ServiceFyziklaniSubmit */
    private $serviceFyziklaniSubmit;
    /** @var ServiceFyziklaniTask */
    private $serviceFyziklaniTask;
    /** @var ServiceFyziklaniTeam */
    private $serviceFyziklaniTeam;
    /** @var User */
    private $user;

    /**
     * TaskCodeHandler constructor.
     * @param ServiceFyziklaniTeam $serviceFyziklaniTeam
     * @param ServiceFyziklaniTask $serviceFyziklaniTask
     * @param ServiceFyziklaniSubmit $serviceFyziklaniSubmit
     * @param User $user
     */
    public function __construct(
        ServiceFyziklaniTeam $serviceFyziklaniTeam,
        ServiceFyziklaniTask $serviceFyziklaniTask,
        ServiceFyziklaniSubmit $serviceFyziklaniSubmit,
        User $user
    ) {
        $this->serviceFyziklaniTeam = $serviceFyziklaniTeam;
        $this->serviceFyziklaniTask = $serviceFyziklaniTask;
        $this->serviceFyziklaniSubmit = $serviceFyziklaniSubmit;
        $this->user = $user;
    }

    public function create(ModelEvent $event): Handler {
        return new Handler($event, $this->serviceFyziklaniTeam, $this->serviceFyziklaniTask, $this->serviceFyziklaniSubmit, $this->user);
    }
}
