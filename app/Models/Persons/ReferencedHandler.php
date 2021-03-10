<?php

namespace FKSDB\Models\Persons;

use Fykosak\NetteORM\AbstractModel;
use FKSDB\Models\ORM\IModel;
use Nette\Utils\ArrayHash;

/**
 * Due to author's laziness there's no class doc (or it's self explaining).
 *
 * @author Michal Koutný <michal@fykos.cz>
 */
interface ReferencedHandler {

    public const RESOLUTION_OVERWRITE = 'overwrite';
    public const RESOLUTION_KEEP = 'keep';
    public const RESOLUTION_EXCEPTION = 'exception';

    public function getResolution(): string;

    public function setResolution(string $resolution): void;

    public function update(IModel $model, ArrayHash $values): void;

    public function createFromValues(ArrayHash $values): AbstractModel;

    public function isSecondaryKey(string $field): bool;

    public function findBySecondaryKey(string $field, string $key): ?AbstractModel;
}
