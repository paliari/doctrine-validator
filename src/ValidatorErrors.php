<?php
namespace Paliari\Doctrine;

class ValidatorErrors
{

    public static $_default_messages = [
        'inclusion'                => 'is not included in the list',
        'exclusion'                => 'is reserved',
        'invalid'                  => 'is invalid',
        'confirmation'             => "doesn't match confirmation",
        'accepted'                 => 'must be accepted',
        'empty'                    => "can't be empty",
        'blank'                    => "can't be blank",
        'too_long'                 => 'is too long (maximum is %{count} characters)',
        'too_short'                => 'is too short (minimum is %{count} characters)',
        'wrong_length'             => 'is the wrong length (should be %{count} characters)',
        'taken'                    => 'has already been taken',
        'not_a_number'             => 'is not a number',
        'not_a_integer'            => 'is not a integer',
        'greater_than'             => 'must be greater than %{count}',
        'equal_to'                 => 'must be equal to %{count}',
        'less_than'                => 'must be less than %{count}',
        'odd'                      => 'must be odd',
        'even'                     => 'must be even',
        'unique'                   => 'must be unique',
        'less_than_or_equal_to'    => 'must be less than or equal to %{count}',
        'greater_than_or_equal_to' => 'must be greater than or equal to %{count}',
    ];

    public $model_name = '';

    protected $messages = [];

    public static function getDefaultMessage($key)
    {
        return static::$_default_messages[$key];
    }

    public function isValid()
    {
        return empty($this->messages);
    }

    /**
     * @param string $attribute
     * @param string $msg
     *
     * @return $this
     */
    public function add($attribute, $msg)
    {
        if (!($msg)) {
            $msg = static::getDefaultMessage('invalid');
        }
        $this->messages[$attribute][] = $msg;

        return $this;
    }

    /**
     * @return array
     */
    public function asJson()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->messages;
    }

    public function __toString()
    {
        $str = '';
        foreach ($this->messages as $k => $errors) {
            $model = $this->model_name;
            $str .= $model::humAttribute($k) . ': ' . implode(', ', $errors);
        }

        return $str;
    }
}
