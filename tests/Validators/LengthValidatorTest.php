<?php

class LengthValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param mixed $value
     * @param int   $length
     * @param bool  $expected
     *
     * @dataProvider checkMinimumProvider
     */
    public function testCheckMinimum($value, $length, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkMinimum($value, $length));
        } else {
            $this->assertFalse($this->validator()->checkMinimum($value, $length));
        }
    }

    public function checkMinimumProvider()
    {
        return [
            ['123', 3, true],
            ['1234', 3, true],
            ['não', 3, true],
            ['', 0, true],
            ['1', 0, true],
            ['1234567890', 10, true],
            ['12345678901', 10, true],
            ['123456789', 10, false],
            ['1', 2, false],
            ['áçãé`à%@#*&!~õ', 14, true],
        ];
    }

    /**
     * @param mixed $value
     * @param int   $length
     * @param bool  $expected
     *
     * @dataProvider checkMaximumProvider
     */
    public function testCheckMaximum($value, $length, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkMaximum($value, $length));
        } else {
            $this->assertFalse($this->validator()->checkMaximum($value, $length));
        }
    }

    public function checkMaximumProvider()
    {
        return [
            ['123', 3, true],
            ['não', 3, true],
            ['çãé', 3, true],
            ['12', 3, true],
            ['', 3, true],
            ['1234', 3, false],
            ['12', 1, false],
            ['12345678901', 10, false],
            ['1234567890', 10, true],
            ['áçãé`à%@#*&!~õ', 14, true],
        ];
    }

    /**
     * @param mixed $value
     * @param int   $length
     * @param bool  $expected
     *
     * @dataProvider checkEqualProvider
     */
    public function testCheckEqual($value, $length, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkEqual($value, $length));
        } else {
            $this->assertFalse($this->validator()->checkEqual($value, $length));
        }
    }

    public function checkEqualProvider()
    {
        return [
            ['123', 3, true],
            ['não', 3, true],
            ['çãé', 3, true],
            ['12', 3, false],
            ['ão', 3, false],
            ['ã', 3, false],
            ['1234', 3, false],
            ['1234567890', 10, true],
            ['áçãé`à%@#*&!~õ', 14, true],
            ['áçãé`à%@#*&!~õ1', 14, false],
            ['áçãé`à%@#*&!~õ', 15, false],
            ['áçãé`à%@#*&!~õ', 16, false],
            ['áçãé`à%@#*&!~õ', 17, false],
            ['áçãé`à%@#*&!~õ', 18, false],
            ['áçãé`à%@#*&!~õ', 19, false],
            ['áçãé`à%@#*&!~õ', 20, false],
            ['áçãé`à%@#*&!~õ', 21, false],
            ['áçãé`à%@#*&!~õ', 22, false],
            ['áçãé`à%@#*&!~õ', 23, false],
            ['áçãé`à%@#*&!~õ', 13, false],
            ['áçãé`à%@#*&!~õ', 12, false],
            ['áçãé`à%@#*&!~õ', 11, false],
        ];
    }

    /**
     * @param mixed $value
     * @param int   $expected
     *
     * @dataProvider lenProvider
     */
    public function testClen($value, $expected)
    {
        $this->assertEquals($expected, $this->validator()->len($value));
    }

    public function lenProvider()
    {
        return [
            ['áçãé`à%@#*&!~õ', 14],
            ['áçãé`à%@#*&!~õ1', 15],
            ['não', 3],
            ['123', 3],
            ['abc', 3],
        ];
    }

    protected function validator()
    {
        return Paliari\Doctrine\Validators\LengthValidator::instance();
    }

}
