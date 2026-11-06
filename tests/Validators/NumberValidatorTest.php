<?php

use PHPUnit\Framework\Attributes\DataProvider;

class NumberValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param mixed $value
     * @param bool  $expected
     */
    #[DataProvider('checkIntegerProvider')]
    public function testCheckInteger($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkInteger($value));
        } else {
            $this->assertFalse($this->validator()->checkInteger($value));
        }
    }

    public static function checkIntegerProvider()
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
     */
    #[DataProvider('checkNumberProvider')]
    public function testCheckNumber($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkNumber($value));
        } else {
            $this->assertFalse($this->validator()->checkNumber($value));
        }
    }

    public static function checkNumberProvider()
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
     */
    #[DataProvider('checkFloatProvider')]
    public function testCheckFloat($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkFloat($value));
        } else {
            $this->assertFalse($this->validator()->checkFloat($value));
        }
    }

    public static function checkFloatProvider()
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
     */
    #[DataProvider('checkGreaterThanProvider')]
    public function testCheckGreaterThan($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkGreaterThan($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkGreaterThan($value, $than));
        }
    }

    public static function checkGreaterThanProvider()
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
     */
    #[DataProvider('checkGreaterThanOrEqualToProvider')]
    public function testCheckGreaterThanOrEqualTo($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkGreaterThanOrEqualTo($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkGreaterThanOrEqualTo($value, $than));
        }
    }

    public static function checkGreaterThanOrEqualToProvider()
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
     */
    #[DataProvider('checkLessThanProvider')]
    public function testCheckLessThan($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkLessThan($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkLessThan($value, $than));
        }
    }

    public static function checkLessThanProvider()
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
     */
    #[DataProvider('checkLessThanOrEqualToProvider')]
    public function testCheckLessThanOrEqualTo($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkLessThanOrEqualTo($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkLessThanOrEqualTo($value, $than));
        }
    }

    public static function checkLessThanOrEqualToProvider()
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
