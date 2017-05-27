<?php

class NumberValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkIntegerProvider
     */
    public function testCheckInteger($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkInteger($value));
        } else {
            $this->assertFalse($this->validator()->checkInteger($value));
        }
    }

    public function checkIntegerProvider()
    {
        return [
            ['123', true],
            ['1', true],
            [1, true],
            [1.1, false],
            [.1, false],
            [0.1, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkNumberProvider
     */
    public function testCheckNumber($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkNumber($value));
        } else {
            $this->assertFalse($this->validator()->checkNumber($value));
        }
    }

    public function checkNumberProvider()
    {
        return [
            ['123', true],
            ['1', true],
            ['1a', false],
            ['a', false],
            ['', false],
            [1, true],
            [1.1, true],
            [.1, true],
            [0.1, true],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkFloatProvider
     */
    public function testCheckFloat($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkFloat($value));
        } else {
            $this->assertFalse($this->validator()->checkFloat($value));
        }
    }

    public function checkFloatProvider()
    {
        return [
            ['123', true],
            ['1', true],
            ['1a', false],
            ['a', false],
            ['', false],
            [1, true],
            [1.1, true],
            [.1, true],
            [0.1, true],
        ];
    }

    /**
     * @param mixed $value
     * @param int   $than
     * @param bool  $expected
     *
     * @dataProvider checkGreaterThanProvider
     */
    public function testCheckGreaterThan($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkGreaterThan($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkGreaterThan($value, $than));
        }
    }

    public function checkGreaterThanProvider()
    {
        return [
            ['12', 10, true],
            ['11', 10, true],
            [11, 10, true],
            [10, 10, false],
            [9, 10, false],
            [0, 10, false],
            [-1, 10, false],
            [-10, 10, false],
            [-101, 10, false],
        ];
    }

    /**
     * @param mixed $value
     * @param int   $than
     * @param bool  $expected
     *
     * @dataProvider checkGreaterThanOrEqualToProvider
     */
    public function testCheckGreaterThanOrEqualTo($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkGreaterThanOrEqualTo($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkGreaterThanOrEqualTo($value, $than));
        }
    }

    public function checkGreaterThanOrEqualToProvider()
    {
        return [
            ['12', 10, true],
            ['11', 10, true],
            [11, 10, true],
            [10, 10, true],
            [9, 10, false],
            [0, 10, false],
            [-1, 10, false],
            [-10, 10, false],
            [-101, 10, false],
        ];
    }

    /**
     * @param mixed $value
     * @param int   $than
     * @param bool  $expected
     *
     * @dataProvider checkLessThanProvider
     */
    public function testCheckLessThan($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkLessThan($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkLessThan($value, $than));
        }
    }

    public function checkLessThanProvider()
    {
        return [
            ['12', 10, false],
            ['11', 10, false],
            [11, 10, false],
            [10, 10, false],
            [9, 10, true],
            [0, 10, true],
            [-1, 10, true],
            [-10, 10, true],
            [-101, 10, true],
        ];
    }

    /**
     * @param mixed $value
     * @param int   $than
     * @param bool  $expected
     *
     * @dataProvider checkLessThanOrEqualToProvider
     */
    public function testCheckLessThanOrEqualTo($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkLessThanOrEqualTo($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkLessThanOrEqualTo($value, $than));
        }
    }

    public function checkLessThanOrEqualToProvider()
    {
        return [
            ['12', 10, false],
            ['11', 10, false],
            [11, 10, false],
            [10, 10, true],
            [9, 10, true],
            [0, 10, true],
            [-1, 10, true],
            [-10, 10, true],
            [-101, 10, true],
        ];
    }

    protected function validator()
    {
        return Paliari\Doctrine\Validators\NumberValidator::instance();
    }

}
