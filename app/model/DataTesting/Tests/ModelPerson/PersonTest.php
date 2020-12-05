<?php

namespace FKSDB\DataTesting\Tests\ModelPerson;

use FKSDB\Logging\ILogger;
use FKSDB\ORM\Models\ModelPerson;

/**
 * Class PersonTest
 * @author Michal Červeňák <miso@fykos.cz>
 */
abstract class PersonTest {

    public string $id;

    public string $title;

    public function __construct(string $id, string $title) {
        $this->id = $id;
        $this->title = $title;
    }

    abstract public function run(ILogger $logger, ModelPerson $person): void;
}
