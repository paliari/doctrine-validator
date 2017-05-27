<?php
namespace Paliari\Doctrine\Validators;

class BaseValidator
{

    protected static $_instances = [];

    /**
     * @return static
     */
    public static function instance()
    {
        $className = get_called_class();
        if (!isset(static::$_instances[$className])) {
            static::$_instances[$className] = new static();
        }

        return static::$_instances[$className];
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isBlank($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        if ('0' == $value) {
            return false;
        }

        return empty($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isPresence($value)
    {
        return !$this->isBlank($value);
    }

    /**
     * @param mixed $value
     * @param array $in
     *
     * @return bool
     */
    public function isIncluded($value, $in)
    {
        return in_array($value, $in, true);
    }

    /**
     * @param mixed $value
     * @param array $in
     *
     * @return bool
     */
    public function isExcluded($value, $in)
    {
        return !$this->isIncluded($value, $in);
    }

}
