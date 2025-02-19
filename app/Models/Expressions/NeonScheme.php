<?php

namespace FKSDB\Models\Expressions;

use Nette\InvalidArgumentException;
use Nette\Utils\Arrays;

/**
 * So far only helper methods to "checked" laoding of Neon configuration.
 * The scheme (metamodel) for the configuration is Neon-encoded as well.
 */
class NeonScheme {

    public const TYPE_NEON = 'neon';
    public const TYPE_EXPRESSION = 'expression';
    public const QUALIFIER_ARRAY = 'array';

    /**
     * @throws NeonSchemaException
     */
    public static function readSection(array $section, array $sectionScheme): array {
        if (!is_array($section)) {
            throw new NeonSchemaException('Expected array got \'' . (string)$section . '\'.');
        }
        $result = [];
        foreach ($sectionScheme as $key => $metadata) {

            if ($metadata === null || !array_key_exists('default', $metadata)) {
                try {
                    $result[$key] = Arrays::get($section, $key);
                } catch (InvalidArgumentException $exception) {
                    throw new NeonSchemaException("Expected key '$key' not found.", null, $exception);
                }
                if ($metadata === null) {
                    continue;
                }
            } else {
                $result[$key] = isset($section[$key]) ? $section[$key] : $metadata['default'];
            }

            $typeDef = $metadata['type'] ?? self::TYPE_NEON;
            $typeDef = explode(' ', $typeDef);
            $type = $typeDef[0];
            $qualifier = $typeDef[1] ?? null;

            if ($type == self::TYPE_EXPRESSION) {
                if ($qualifier == self::QUALIFIER_ARRAY) {
                    $result[$key] = array_map(function ($it) {
                        return Helpers::statementFromExpression($it);
                    }, $result[$key]);
                } elseif ($qualifier === null) {
                    $result[$key] = Helpers::statementFromExpression($result[$key]);
                } else {
                    throw new NeonSchemaException("Unknown type qualifier '$qualifier'.");
                }
            } elseif ($type != self::TYPE_NEON) {
                throw new NeonSchemaException("Unknown type '$type'.");
            }
        }
        $unknown = array_diff(array_keys($section), array_keys($sectionScheme));
        if ($unknown) {
            throw new NeonSchemaException('Unknown key(s): ' . implode(', ', $unknown) . '.');
        }
        return $result;
    }
}
