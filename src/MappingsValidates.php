<?php

namespace Paliari\Doctrine;

use Doctrine\DBAL\Types\Types;

class MappingsValidates
{
    public static $cache = [];

    protected static $_type_int = [Types::INTEGER, Types::BIGINT, Types::SMALLINT];
    protected static $_type_numeric = [Types::FLOAT, Types::DECIMAL];
    protected static $_type_string = [Types::STRING, Types::TEXT];

    /**
     * @param string $model_name
     * @param string $validates_name
     *
     * @return array
     */
    public static function getDefaults($model_name, $validates_name)
    {
        if (!isset(static::$cache[$model_name][$validates_name])) {
            static::extractDefaultsValidates($model_name);
        }

        return static::$cache[$model_name][$validates_name];
    }

    /**
     * @param string $model_name
     */
    protected static function extractDefaultsValidates($model_name)
    {
        $length = $numericality = $inclusion = [];
        foreach ($model_name::getEm()->getClassMetadata($model_name)->fieldMappings as $field => $map) {
            $type = static::mappingValue($map, 'type');
            if (!static::mappingValue($map, 'id')) {
                $nullable = (bool)static::mappingValue($map, 'nullable');
                $max_length = static::mappingValue($map, 'length');
                if ($max_length && in_array($type, static::$_type_string)) {
                    $length[$field] = ['maximum' => $max_length, 'allow_blank' => $nullable];
                }
                $is_int = in_array($type, static::$_type_int);
                if ($is_int || in_array($type, static::$_type_numeric)) {
                    $numericality[$field] = ['only_integer' => $is_int, 'allow_blank' => $nullable];
                }
                if (Types::BOOLEAN == $type) {
                    $inclusion[$field] = ['in' => [true, false], 'allow_blank' => $nullable];
                }
            }
        }
        static::$cache[$model_name] = [
            'validates_length_of' => $length,
            'validates_numericality_of' => $numericality,
            'validates_inclusion_of' => $inclusion,
        ];
    }

    /**
     * ORM 2 exposes field mappings as arrays and ORM 3 as FieldMapping objects,
     * whose ArrayAccess is deprecated.
     */
    protected static function mappingValue(array|object $map, string $key): mixed
    {
        return is_array($map) ? ($map[$key] ?? null) : ($map->$key ?? null);
    }
}
