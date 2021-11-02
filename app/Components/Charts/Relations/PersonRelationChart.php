<?php

declare(strict_types=1);

namespace FKSDB\Components\Charts\Relations;

use FKSDB\Models\ORM\Services\ServicePerson;
use Fykosak\Utils\FrontEndComponents\FrontEndComponent;
use Nette\Database\Explorer;
use Tracy\Debugger;

class PersonRelationChart extends FrontEndComponent
{

    private Explorer $explorer;
    private ServicePerson $servicePerson;

    public function injectBase(Explorer $explorer, ServicePerson $servicePerson): void
    {
        $this->explorer = $explorer;
    }

    protected function getData(): array
    {
        foreach ($this->explorer->table('person_relation') as $row) {
            $fromPersonRow = $row->ref('person', 'from_person_id');
            $toPersonRow = $row->ref('person', 'to_person_id');
            Debugger::barDump($fromPersonRow);
            Debugger::barDump($toPersonRow);
        }
    }
}
