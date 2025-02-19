<?php

namespace FKSDB\Components\Forms\Controls\Autocomplete;

interface FilteredDataProvider extends DataProvider {

    /**
     * @return array see parent + filtered by the user input
     */
    public function getFilteredItems(?string $search): array;
}
