<?php

declare(strict_types=1);

namespace FKSDB\Components\Charts\Relations;

use FKSDB\Components\Charts\Core\Chart;
use FKSDB\Models\ORM\Models\ModelPerson;
use FKSDB\Models\ORM\Services\ServicePerson;
use Fykosak\Utils\FrontEndComponents\FrontEndComponent;
use Nette\Database\Explorer;
use Nette\DI\Container;

class PersonRelationChart extends FrontEndComponent implements Chart
{

    private Explorer $explorer;
    private ServicePerson $servicePerson;

    public function __construct(Container $container)
    {
        parent::__construct($container, 'chart.person-relation');
    }

    public function injectBase(Explorer $explorer, ServicePerson $servicePerson): void
    {
        $this->servicePerson = $servicePerson;
        $this->explorer = $explorer;
    }

    protected function getData(): array
    {
        $nodes = [];
        $links = [];
        foreach ($this->explorer->table('person_relation')->order('level') as $row) {
            if ($row->level === 1 || $row->level === 4) {
               // $links[] = ['from' => $row->to_person_id, 'to' => $row->from_person_id, 'level' => $row->level];
            }
            $links[] = ['from' => $row->from_person_id, 'to' => $row->to_person_id, 'level' => $row->level];
            if (!isset($nodes[$row->from_person_id])) {
                $fromPerson = ModelPerson::createFromActiveRow($row->ref('person', 'from_person_id'));
                $nodes[$fromPerson->person_id] = $this->serialisePerson($fromPerson);
            }
            if (!isset($nodes[$row->to_person_id])) {
                $toPerson = ModelPerson::createFromActiveRow($row->ref('person', 'to_person_id'));
                $nodes[$toPerson->person_id] = $this->serialisePerson($toPerson);
            }
            $nodes[44] = $this->serialisePerson($this->servicePerson->findByPrimary(44));
        }
        return ['nodes' => $nodes, 'links' => $links];
    }

    private function serialisePerson(ModelPerson $person): array
    {
        return [
            'name' => $person->getFullName(),
            'gender' => $person->gender,
        ];
    }

    public function getTitle(): string
    {
        return 'Person relation';
    }

    public function getDescription(): ?string
    {
        return null;
    }
}
