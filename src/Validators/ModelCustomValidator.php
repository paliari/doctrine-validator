<?php

namespace Paliari\Doctrine\Validators;

/**
 * Class ModelCustomValidator
 *
 * @package Db\Validators
 */
class ModelCustomValidator
{

    protected $_validators = [];

    protected static $_instance;

    /**
     * @return static
     */
    public static function i()
    {
        if (!isset(static::$_instance)) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * @param string   $key
     * @param callable $validator deve receber o model no parametro
     *
     * @return $this
     */
    public function add(string $key, callable $validator)
    {
        $this->_validators[$key][] = $validator;

        return $this;
    }

    /**
     * @param string $key
     * @param object $model
     */
    public function run(string $key, $model)
    {
        $validators = isset($this->_validators[$key]) ? $this->_validators[$key] : [];
        foreach ($validators as $call) {
            call_user_func($call, $model);
        }
    }

}
