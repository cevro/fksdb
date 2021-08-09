<?php

declare(strict_types=1);

namespace FKSDB\Models\ORM\Services;

use FKSDB\Models\ORM\Models\ModelEmailMessage;
use Fykosak\NetteORM\AbstractService;
use Fykosak\NetteORM\Exceptions\ModelException;
use Fykosak\NetteORM\TypedTableSelection;
use Nette\Database\Table\ActiveRow;

/**
 * @method ModelEmailMessage createNewModel(array $data)
 */
class ServiceEmailMessage extends AbstractService
{

    public function getMessagesToSend(int $limit): TypedTableSelection
    {
        return $this->getTable()->where('state', ModelEmailMessage::STATE_WAITING)->limit($limit);
    }

    /**
     * @param array $data
     * @return ModelEmailMessage|ActiveRow
     * @throws ModelException
     */
    public function addMessageToSend(array $data): ModelEmailMessage
    {
        $data['state'] = ModelEmailMessage::STATE_WAITING;
        if (!isset($data['reply_to'])) {
            $data['reply_to'] = $data['sender'];
        }
        return $this->createNewModel($data);
    }
}
