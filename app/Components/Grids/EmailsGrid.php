<?php

declare(strict_types=1);

namespace FKSDB\Components\Grids;

use FKSDB\Models\Exceptions\BadTypeException;
use FKSDB\Models\ORM\Services\ServiceEmailMessage;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use NiftyGrid\DuplicateButtonException;
use NiftyGrid\DuplicateColumnException;

class EmailsGrid extends EntityGrid
{

    public function __construct(Container $container)
    {
        parent::__construct($container, ServiceEmailMessage::class, [
            'email_message.email_message_id',
            'email_message.recipient',
            'email_message.subject',
            'email_message.state',
        ]);
    }

    /**
     * @throws BadTypeException
     * @throws DuplicateButtonException
     * @throws DuplicateColumnException
     */
    protected function configure(Presenter $presenter): void
    {
        parent::configure($presenter);
        $this->setDefaultOrder('created DESC');
        $this->addLinkButton('detail', 'detail', _('Detail'), false, ['id' => 'email_message_id']);
        $this->paginate = true;
    }
}
