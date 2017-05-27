<?php
namespace Paliari\Doctrine\Validators;

class NumberValidator extends BaseValidator
{

    /**
     * @param mixed  $value
     * @param string $filter
     *
     * @return bool
     */
    public function check($value, $filter)
    {
        return $this->filter()->check($value, $filter);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkInteger($value)
    {
        return $this->filter()->checkInteger($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkNumber($value)
    {
        return $this->checkFloat($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkFloat($value)
    {
        return $this->filter()->checkFloat($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkGreaterThan($value, $than)
    {
        return $value > $than;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkGreaterThanOrEqualTo($value, $than)
    {
        return $value >= $than;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkLessThan($value, $than)
    {
        return $value < $than;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkLessThanOrEqualTo($value, $than)
    {
        return $value <= $than;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkEqualTo($value, $than)
    {
        return $value == $than;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function checkOtherThan($value, $than)
    {
        return $value != $than;
    }

    /**
     * @return FilterVarValidator
     */
    protected function filter()
    {
        return FilterVarValidator::instance();
    }

}
