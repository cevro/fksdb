<?php

namespace FKSDB\Config\Extensions;

use FKSDB\Components\Controls\Stalking\StalkingService;
use FKSDB\Components\DatabaseReflection\AbstractRow;
use FKSDB\Components\DatabaseReflection\StringRow;
use Nette\Config\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\NotImplementedException;
use Tracy\Debugger;

/**
 * Class StalkingExtension
 * @package FKSDB\Config\Extensions
 */
class DBReflectionExtension extends CompilerExtension {

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        foreach ($this->config['tables'] as $tableName => $fields) {
            foreach ($fields as $fieldName => $field) {
                Debugger::barDump($field);
                $factory = null;
                if (is_array($field)) {
                    switch ($field['type']) {
                        case 'string':
                            $this->registerStringRow($builder, $tableName, $fieldName, $field);
                            continue;
                        default:
                            throw new NotImplementedException();
                    }
                }
                if (is_string($field) && preg_match('/([A-Za-z0-9]+\\\\)*/', $field)) {
                    $builder->addDefinition($this->prefix($tableName . '.' . $fieldName))
                        ->setFactory($field);
                    continue;
                }
            }
        }
    }

    /**
     * @param ContainerBuilder $builder
     * @param string $tableName
     * @param string $fieldName
     * @param array $field
     */
    private function registerStringRow(ContainerBuilder $builder, string $tableName, string $fieldName, array $field) {
        $builder->addDefinition($this->prefix($tableName . '.' . $fieldName))
            ->setFactory(StringRow::class)
            ->addSetup('setUp', [
                $tableName,
                $field['title'],
                isset($field['accessKey']) ? $field['accessKey'] : $fieldName,
                isset($field['description']) ? $field['description'] : null
            ]);
    }
}
