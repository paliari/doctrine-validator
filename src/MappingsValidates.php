<?php

namespace Paliari\Doctrine;

use Doctrine\DBAL\Types\Type;
use Paliari\Utils\A;

class MappingsValidates
{

    public static $cache = [];

    protected static $_type_int     = [Type::INTEGER, Type::BIGINT, Type::SMALLINT];
    protected static $_type_numeric = [Type::FLOAT, Type::DECIMAL];
    protected static $_type_string  = [Type::STRING, Type::TEXT];

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
            $type = $map['type'];
            if (!A::get($map, 'id')) {
                if (A::get($map, 'length') && in_array($type, static::$_type_string)) {
                    $length[$field] = ['maximum' => $map['length'], 'allow_blank' => $map['nullable']];
                }
                $is_int = in_array($type, static::$_type_int);
                if ($is_int || in_array($type, static::$_type_numeric)) {
                    $numericality[$field] = ['only_integer' => $is_int, 'allow_blank' => $map['nullable']];
                }
                if (Type::BOOLEAN == $type) {
                    $inclusion[$field] = ['in' => [true, false], 'allow_blank' => $map['nullable']];
                }
            }
        }
        static::$cache[$model_name] = [
            'validates_length_of'       => $length,
            'validates_numericality_of' => $numericality,
            'validates_inclusion_of'    => $inclusion,
        ];
    }

}
