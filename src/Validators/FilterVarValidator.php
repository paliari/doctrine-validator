<?php

namespace Paliari\Doctrine\Validators;

use Exception;

class FilterVarValidator extends BaseValidator
{

    const BOOLEAN     = 'boolean';
    const EMAIL       = 'email';
    const FLOAT       = 'float';
    const INTEGER     = 'integer';
    const IP          = 'ip';
    const MAC_ADDRESS = 'mac_address';
    const URL         = 'url';

    protected static $_customs = [];

    protected static $_types = [
        self::BOOLEAN     => FILTER_VALIDATE_BOOLEAN,
        self::EMAIL       => FILTER_VALIDATE_EMAIL,
        self::FLOAT       => FILTER_VALIDATE_FLOAT,
        self::INTEGER     => FILTER_VALIDATE_INT,
        self::IP          => FILTER_VALIDATE_IP,
        self::MAC_ADDRESS => FILTER_VALIDATE_MAC,
        self::URL         => FILTER_VALIDATE_URL,
    ];

    public static function add($filter, $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('$callback not is callable!');
        }
        static::$_customs[$filter] = $callback;
    }

    /**
     * @param mixed  $value
     * @param string $filter
     *
     * @return bool
     */
    public function check($value, $filter)
    {
        if (isset(static::$_types[$filter])) {
            return null !== filter_var($value, static::$_types[$filter], FILTER_NULL_ON_FAILURE);
        }
        if (isset(static::$_customs[$filter])) {
            return $this->checkCustom($value, $filter);
        }

        return 1 === preg_match($filter, $value);
    }

    /**
     * @param mixed  $value
     * @param string $filter
     *
     * @return bool
     */
    public function checkCustom($value, $filter)
    {
        return call_user_func_array(static::$_customs[$filter], [$value]);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkBoolean($value)
    {
        return $this->check($value, self::BOOLEAN);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkEmail($value)
    {
        return $this->check($value, self::EMAIL);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkFloat($value)
    {
        return $this->check($value, self::FLOAT);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkInteger($value)
    {
        return $this->check($value, self::INTEGER);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkIp($value)
    {
        return $this->check($value, self::IP);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkMacAddress($value)
    {
        return $this->check($value, self::MAC_ADDRESS);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkUrl($value)
    {
        return $this->check($value, self::URL);
    }

}
