<?php

namespace Persons;

use FKSDB\ORM\Services\ServiceEventPersonAccommodation;
use FKSDB\ORM\Services\ServicePerson;
use FKSDB\ORM\Services\ServicePersonHistory;
use FKSDB\ORM\Services\ServicePersonInfo;
use Nette\SmartObject;
use ServiceMPersonHasFlag;
use ServiceMPostContact;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
class ReferencedPersonHandlerFactory {
    use SmartObject;
    /**
     * @var \FKSDB\ORM\Services\ServicePerson
     */
    private $servicePerson;

    /**
     * @var \FKSDB\ORM\Services\ServicePersonInfo
     */
    private $servicePersonInfo;

    /**
     * @var \FKSDB\ORM\Services\ServicePersonHistory
     */
    private $servicePersonHistory;

    /**
     * @var ServiceMPostContact
     */
    private $serviceMPostContact;

    /**
     * @var ServiceMPersonHasFlag
     */
    private $serviceMPersonHasFlag;
    /**
     * @var ServiceEventPersonAccommodation
     */
    private $serviceEventPersonAccommodation;
    /**
     * @var \FKSDB\Components\Forms\Controls\Schedule\Handler
     */
    private $eventScheduleHandler;

    /**
     * ReferencedPersonHandlerFactory constructor.
     * @param ServiceEventPersonAccommodation $serviceEventPersonAccommodation
     * @param ServicePerson $servicePerson
     * @param ServicePersonInfo $servicePersonInfo
     * @param \FKSDB\ORM\Services\ServicePersonHistory $servicePersonHistory
     * @param ServiceMPostContact $serviceMPostContact
     * @param ServiceMPersonHasFlag $serviceMPersonHasFlag
     * @param \FKSDB\Components\Forms\Controls\Schedule\Handler $eventScheduleHandler
     */
    function __construct(
        ServiceEventPersonAccommodation $serviceEventPersonAccommodation,
        ServicePerson $servicePerson,
        ServicePersonInfo $servicePersonInfo,
        ServicePersonHistory $servicePersonHistory,
        ServiceMPostContact $serviceMPostContact,
        ServiceMPersonHasFlag $serviceMPersonHasFlag,
        \FKSDB\Components\Forms\Controls\Schedule\Handler $eventScheduleHandler
    ) {
        $this->servicePerson = $servicePerson;
        $this->servicePersonInfo = $servicePersonInfo;
        $this->servicePersonHistory = $servicePersonHistory;
        $this->serviceMPostContact = $serviceMPostContact;
        $this->serviceMPersonHasFlag = $serviceMPersonHasFlag;
        $this->serviceEventPersonAccommodation = $serviceEventPersonAccommodation;
        $this->eventScheduleHandler = $eventScheduleHandler;
    }

    /**
     * @param $acYear
     * @param string $resolution
     * @param $eventId
     * @return ReferencedPersonHandler
     */
    public function create($acYear, $resolution = ReferencedPersonHandler::RESOLUTION_EXCEPTION, $eventId) {
        $handler = new ReferencedPersonHandler(
            $this->serviceEventPersonAccommodation,
            $this->servicePerson,
            $this->servicePersonInfo,
            $this->servicePersonHistory,
            $this->serviceMPostContact,
            $this->serviceMPersonHasFlag,
            $this->eventScheduleHandler,
            $acYear,
            $resolution
        );
        $handler->setEventId($eventId);
        return $handler;
    }

}

