<?php
namespace Paliari\Doctrine\Validators;

class LengthValidator extends BaseValidator
{

    public static $ENCODING = 'UTF-8';

    /**
     * @param string $value
     * @param int    $minimum
     *
     * @return bool
     */
    public function checkMinimum($value, $minimum)
    {
        return $this->len($value) >= $minimum;
    }

    /**
     * @param string $value
     * @param int    $maximum
     *
     * @return bool
     */
    public function checkMaximum($value, $maximum)
    {
        return $this->len($value) <= $maximum;
    }

    /**
     * @param string $value
     * @param int    $length
     *
     * @return bool
     */
    public function checkEqual($value, $length)
    {
        return $this->len($value) == $length;
    }

    /**
     * @param string $value
     *
     * @return int
     */
    public function len($value)
    {
        return $this->strLen($value, static::$ENCODING);
    }

    /**
     * @param string $value
     * @param string $encoding
     *
     * @return int
     */
    protected function strLen($value, $encoding)
    {
        return mb_strlen($value, $encoding);
    }

}
