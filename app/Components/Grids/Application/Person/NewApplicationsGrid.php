<?php

namespace FKSDB\Components\Grids\Application\Person;

use FKSDB\Components\Grids\BaseGrid;
use FKSDB\Config\NeonSchemaException;
use FKSDB\Events\EventDispatchFactory;
use FKSDB\Events\Machine\BaseMachine;
use FKSDB\Exceptions\BadTypeException;
use FKSDB\ORM\Models\ModelEvent;
use FKSDB\ORM\Services\ServiceEvent;
use Nette\Application\IPresenter;
use Nette\Application\UI\InvalidLinkException;
use NiftyGrid\DataSource\IDataSource;
use NiftyGrid\DataSource\NDataSource;
use NiftyGrid\DuplicateButtonException;
use NiftyGrid\DuplicateColumnException;

/**
 * Class NewApplicationsGrid
 * @author Michal Červeňák <miso@fykos.cz>
 */
class NewApplicationsGrid extends BaseGrid {

    protected ServiceEvent $serviceEvent;

    protected EventDispatchFactory $eventDispatchFactory;

    final public function injectPrimary(ServiceEvent $serviceEvent, EventDispatchFactory $eventDispatchFactory): void {
        $this->serviceEvent = $serviceEvent;
        $this->eventDispatchFactory = $eventDispatchFactory;
    }

    protected function getData(): IDataSource {
        $events = $this->serviceEvent->getTable()
            ->where('registration_begin <= NOW()')
            ->where('registration_end >= NOW()');
        return new NDataSource($events);
    }

    /**
     * @param IPresenter $presenter
     * @return void
     * @throws BadTypeException
     * @throws DuplicateButtonException
     * @throws DuplicateColumnException
     * @throws NeonSchemaException
     * @throws InvalidLinkException
     */
    protected function configure(IPresenter $presenter): void {
        parent::configure($presenter);
        $this->paginate = false;
        $this->addColumns([
            'event.name',
            'contest.contest',
        ]);
        $this->addButton('create')
            ->setText(_('Create application'))
            ->setLink(function (ModelEvent $row): string {
                return $this->getPresenter()->link(':Public:Application:default', ['eventId' => $row->event_id]);
            })
            ->setShow(function (ModelEvent $modelEvent): bool {
                $holder = $this->eventDispatchFactory->getDummyHolder($modelEvent);
                $machine = $this->eventDispatchFactory->getEventMachine($modelEvent);
                $transitions = $machine->getPrimaryMachine()->getAvailableTransitions($holder, BaseMachine::STATE_INIT, BaseMachine::EXECUTABLE | BaseMachine::VISIBLE);
                return (bool)count($transitions);
            });
    }
}
