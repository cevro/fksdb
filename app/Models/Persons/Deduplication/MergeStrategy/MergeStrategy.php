<?php

namespace FKSDB\Models\Persons\Deduplication\MergeStrategy;

interface MergeStrategy {

    /**
     * @param mixed $trunk
     * @param mixed $merged
     * @throws CannotMergeException
     */
    public function mergeValues($trunk, $merged);
}
