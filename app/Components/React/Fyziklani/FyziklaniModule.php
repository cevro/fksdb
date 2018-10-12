<?php

namespace FKSDB\Components\React\Fyziklani;

use FKSDB\Components\React\ReactComponent;
use FKSDB\ORM\ModelEvent;
use Nette\DI\Container;

abstract class FyziklaniModule extends ReactComponent {

    /**
     * @var \ServiceBrawlRoom
     */
    private $serviceBrawlRoom;

    /**
     * @var ModelEvent
     */
    private $event;

    /**
     * @var Container
     */
    protected $context;

    public function __construct(Container $container,\ServiceBrawlRoom $serviceBrawlRoom, ModelEvent $event) {
        parent::__construct();
        $this->serviceBrawlRoom = $serviceBrawlRoom;
        $this->event = $event;
        $this->container = $container;
    }


    public final function getModuleName(): string {
        return 'fyziklani';
    }

    protected final function getEvent() {
        return $this->event;
    }

    /**
     * @return \ModelBrawlRoom[]
     */
    protected function getRooms() {
        return $this->serviceBrawlRoom->getRoomsByIds($this->getEvent()->getParameter('rooms'));
    }
}
