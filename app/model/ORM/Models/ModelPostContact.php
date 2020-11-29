<?php

namespace FKSDB\ORM\Models;

use Nette\Database\Table\ActiveRow;

/**
 *
 * @author Michal Koutný <xm.koutny@gmail.com>
 * @property-read ActiveRow address
 */
class ModelPostContact extends AbstractModelSingle {

    public const TYPE_DELIVERY = 'D';
    public const TYPE_PERMANENT = 'P';

    public function getAddress(): ?ModelAddress {
        return $this->address ? ModelAddress::createFromActiveRow($this->address) : null;
    }
}
