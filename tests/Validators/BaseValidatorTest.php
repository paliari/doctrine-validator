<?php

use PHPUnit\Framework\Attributes\DataProvider;

class BaseValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param mixed $value
     * @param bool  $expected
     */
    #[DataProvider('isBlankProvider')]
    public function testIsBlank($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->isBlank($value));
        } else {
            $this->assertFalse($this->validator()->isBlank($value));
        }
    }

    public static function isBlankProvider()
    {
        return [
            ['.', false],
            ['a', false],
            ['0', false],
            ['1', false],
            [0, false],
            [1, false],
            [false, false],
            [new stdClass, false],
            [null, true],
            [[], true],
            ['', true],
            [' ', true],
            ['      ', true],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     */
    #[DataProvider('checkPresenceOfProvider')]
    public function testCheckPresenceOf($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkPresenceOf($value));
        } else {
            $this->assertFalse($this->validator()->checkPresenceOf($value));
        }
    }

    public static function checkPresenceOfProvider()
    {
        return [
            ['.', true],
            ['a', true],
            ['0', true],
            ['1', true],
            [0, true],
            [1, true],
            [false, true],
            [new stdClass, true],
            [null, false],
            [[], false],
            ['', false],
            [' ', false],
            ['      ', false],
        ];
    }

    /**
     * @param mixed $value
     * @param array $in
     * @param bool  $expected
     */
    #[DataProvider('checkIncludedProvider')]
    public function testCheckIncluded($value, $in, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkIncluded($value, $in));
        } else {
            $this->assertFalse($this->validator()->checkIncluded($value, $in));
        }
    }

    public static function checkIncludedProvider()
    {
        return [
            [1, [1, 2], true],
            ['1', [1, 2], false],
            [0, [0, 2], true],
            ['0', [0, 2], false],
            [null, ['', null], true],
            [null, ['', '0', 0], false],
        ];
    }

    /**
     * @param mixed $value
     * @param array $in
     * @param bool  $expected
     */
    #[DataProvider('checkExcludedProvider')]
    public function testCheckExcluded($value, $in, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkExcluded($value, $in));
        } else {
            $this->assertFalse($this->validator()->checkExcluded($value, $in));
        }
    }

    public static function checkExcludedProvider()
    {
        return [
            [1, [1, 2], false],
            ['1', [1, 2], true],
            [0, [0, 2], false],
            ['0', [0, 2], true],
            [null, ['', null], false],
            [null, ['', '0', 0], true],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $than
     * @param bool  $expected
     */
    #[DataProvider('checkEqualToProvider')]
    public function testCheckEqualTo($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkEqualTo($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkEqualTo($value, $than));
        }
    }

    public static function checkEqualToProvider()
    {
        return [
            [1, 1, true],
            [0, 0, true],
            ['0', 0, true],
            ['1', 1, true],
            ['1', 0, false],
            [1, 0, false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $than
     * @param bool  $expected
     */
    #[DataProvider('checkOtherThanProvider')]
    public function testCheckOtherThan($value, $than, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkOtherThan($value, $than));
        } else {
            $this->assertFalse($this->validator()->checkOtherThan($value, $than));
        }
    }

    public static function checkOtherThanProvider()
    {
        return [
            [1, 1, false],
            [0, 0, false],
            ['0', 0, false],
            ['1', 1, false],
            ['1', 0, true],
            [1, 0, true],
        ];
    }

    protected function validator()
    {
        return \Paliari\Doctrine\Validators\BaseValidator::instance();
    }

}
