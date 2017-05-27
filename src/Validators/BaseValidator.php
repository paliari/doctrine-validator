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
    public function checkPresenceOf($value)
    {
        return !$this->isBlank($value);
    }

    /**
     * @param mixed $value
     * @param array $in
     *
     * @return bool
     */
    public function checkIncluded($value, $in)
    {
        return in_array($value, $in, true);
    }

    /**
     * @param mixed $value
     * @param array $in
     *
     * @return bool
     */
    public function checkExcluded($value, $in)
    {
        return !$this->checkIncluded($value, $in);
    }

    /**
     * @param mixed $value
     * @param int   $than
     *
     * @return bool
     */
    public function checkEqualTo($value, $than)
    {
        return $value == $than;
    }

    /**
     * @param mixed $value
     * @param int   $than
     *
     * @return bool
     */
    public function checkOtherThan($value, $than)
    {
        return $value != $than;
    }

}
