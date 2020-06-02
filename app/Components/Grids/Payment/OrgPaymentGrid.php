<?php

namespace FKSDB\Components\Grids\Payment;

use FKSDB\Exceptions\NotImplementedException;
use FKSDB\ORM\DbNames;
use FKSDB\ORM\Models\ModelEvent;
use Nette\Application\UI\InvalidLinkException;
use Nette\DI\Container;
use NiftyGrid\DataSource\IDataSource;
use NiftyGrid\DataSource\NDataSource;
use NiftyGrid\DuplicateButtonException;
use NiftyGrid\DuplicateColumnException;
use NiftyGrid\DuplicateGlobalButtonException;

/**
 * Class OrgPaymentGrid
 * @author Michal Červeňák <miso@fykos.cz>
 */
class OrgPaymentGrid extends PaymentGrid {
    /**
     * @var ModelEvent
     */
    private $event;

    /**
     * OrgPaymentGrid constructor.
     * @param ModelEvent $event
     * @param Container $container
     */
    public function __construct(ModelEvent $event, Container $container) {
        parent::__construct($container);
        $this->event = $event;
    }

    protected function getData(): IDataSource {
        $schools = $this->servicePayment->getTable()->where('event_id', $this->event->event_id);
        return new NDataSource($schools);
    }

    /**
     * @param $presenter
     * @return void
     * @throws DuplicateButtonException
     * @throws DuplicateColumnException
     * @throws DuplicateGlobalButtonException
     * @throws InvalidLinkException
     */
    protected function configure($presenter) {
        parent::configure($presenter);

        $this->addColumns([
            DbNames::TAB_PAYMENT . '.id',
            'referenced.person_name',
            // 'referenced.event_name',
            DbNames::TAB_PAYMENT . '.price',
            DbNames::TAB_PAYMENT . '.state',
            DbNames::TAB_PAYMENT . '.variable_symbol',
        ]);
        $this->addLink('payment.detail', false);
        $this->paginate = false;
        $this->addCSVDownloadButton();
    }
}
