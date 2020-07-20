<?php

namespace Persons;

use FKSDB\Components\Forms\Controls\Schedule\Handler;
use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Services\ServicePerson;
use FKSDB\ORM\Services\ServicePersonHistory;
use FKSDB\ORM\Services\ServicePersonInfo;
use Nette\SmartObject;
use FKSDB\ORM\ServicesMulti\ServiceMPersonHasFlag;
use FKSDB\ORM\ServicesMulti\ServiceMPostContact;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ReferencedPersonHandlerFactory {
    use SmartObject;

    /** @var ServicePerson */
    private $servicePerson;

    /** @var ServicePersonInfo */
    private $servicePersonInfo;

    /** @var ServicePersonHistory */
    private $servicePersonHistory;

    /** @var ServiceMPostContact */
    private $serviceMPostContact;

    /** @var ServiceMPersonHasFlag */
    private $serviceMPersonHasFlag;
    /** @var Handler */
    private $eventScheduleHandler;

    /**
     * ReferencedPersonHandlerFactory constructor.
     * @param ServicePerson $servicePerson
     * @param ServicePersonInfo $servicePersonInfo
     * @param ServicePersonHistory $servicePersonHistory
     * @param ServiceMPostContact $serviceMPostContact
     * @param ServiceMPersonHasFlag $serviceMPersonHasFlag
     * @param Handler $eventScheduleHandler
     */
    public function __construct(
        ServicePerson $servicePerson,
        ServicePersonInfo $servicePersonInfo,
        ServicePersonHistory $servicePersonHistory,
        ServiceMPostContact $serviceMPostContact,
        ServiceMPersonHasFlag $serviceMPersonHasFlag,
        Handler $eventScheduleHandler
    ) {
        $this->servicePerson = $servicePerson;
        $this->servicePersonInfo = $servicePersonInfo;
        $this->servicePersonHistory = $servicePersonHistory;
        $this->serviceMPostContact = $serviceMPostContact;
        $this->serviceMPersonHasFlag = $serviceMPersonHasFlag;
        $this->eventScheduleHandler = $eventScheduleHandler;
    }

    /**
     * @param int $acYear
     * @param string $resolution
     * @param ModelEvent|null $event
     * @return ReferencedPersonHandler
     */
    public function create(int $acYear, $resolution = ReferencedPersonHandler::RESOLUTION_EXCEPTION, ModelEvent $event = null): ReferencedPersonHandler {
        $handler = new ReferencedPersonHandler(
            $this->servicePerson,
            $this->servicePersonInfo,
            $this->servicePersonHistory,
            $this->serviceMPostContact,
            $this->serviceMPersonHasFlag,
            $this->eventScheduleHandler,
            $acYear,
            $resolution
        );
        if ($event) {
            $handler->setEvent($event);
        }
        return $handler;
    }

}
