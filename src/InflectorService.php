<?php

namespace Paliari\Doctrine;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Language;

/**
 * TODO: Receber o inflector por injeção de dependencia
 */
class InflectorService
{
    protected static array $instances = [];

    public static function getContext(string $language = Language::PORTUGUESE): Inflector
    {
        if (!isset(static::$instances[$language])) {
            $factory = InflectorFactory::createForLanguage($language);
            static::$instances[$language] = $factory->build();
        }

        return static::$instances[$language];
    }
}
