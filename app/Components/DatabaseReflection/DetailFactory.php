<?php

namespace FKSDB\Components\DatabaseReflection;

/**
 * Class DetailFactory
 * @package FKSDB\Components\DatabaseReflection
 */
class DetailFactory {
    /**
     * @var array
     */
    private $nodes = [];

    /**
     * @param array $nodes
     * @return void
     */
    public function setNodes(array $nodes) {
        $this->nodes = $nodes;
    }

    public function getSection(string $section): array {
        return $this->nodes[$section];
    }
}
