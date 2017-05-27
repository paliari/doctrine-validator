<?php
use Paliari\Doctrine\Validators\FilterVarValidator as f;

class FilterValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param mixed $value
     * @param mixed $filter
     * @param bool  $expected
     *
     * @dataProvider checkProvider
     */
    public function testCheck($value, $filter, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->check($value, $filter));
        } else {
            $this->assertFalse($this->validator()->check($value, $filter));
        }
    }

    public function checkProvider()
    {
        return [
            [true, f::BOOLEAN, true],
            [false, f::BOOLEAN, true],
            ['false', f::BOOLEAN, true],
            ['true', f::BOOLEAN, true],
            ['on', f::BOOLEAN, true],
            ['off', f::BOOLEAN, true],
            [null, f::BOOLEAN, true],
            [[], f::BOOLEAN, false],
            ['aa@aaa.aa', f::EMAIL, true],
            ['aa@', f::EMAIL, false],
            ['aa@.a', f::EMAIL, false],
            ['aa@a..a', f::EMAIL, false],
            ['aa@a.a.', f::EMAIL, false],
            ['0.0', f::FLOAT, true],
            ['', f::FLOAT, false],
            ['a', f::FLOAT, false],
            [' ', f::FLOAT, false],
            ['0', f::INTEGER, true],
            ['0.1', f::INTEGER, false],
            ['', f::INTEGER, false],
            [' ', f::INTEGER, false],
            ['a', f::INTEGER, false],
            [0, f::INTEGER, true],
            ['127.0.0.1', f::IP, true],
            ['127.0.0.256', f::IP, false],
            ['http://paliari.com', f::URL, true],
            ['paliari.com', f::URL, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkBooleanProvider
     */
    public function testCheckBoolean($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkBoolean($value));
        } else {
            $this->assertFalse($this->validator()->checkBoolean($value));
        }
    }

    public function checkBooleanProvider()
    {
        return [
            [true, true],
            [false, true],
            ['false', true],
            ['true', true],
            ['on', true],
            ['off', true],
            [null, true],
            [[], false],
            [new stdClass, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkEmailProvider
     */
    public function testCheckEmail($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkEmail($value));
        } else {
            $this->assertFalse($this->validator()->checkEmail($value));
        }
    }

    public function checkEmailProvider()
    {
        return [
            ['aa@aaa.aa', true],
            ['aa@', false],
            ['aa@.a', false],
            ['aa@a..a', false],
            ['aa@a.a.', false],
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
            [0.0, true],
            [.0, true],
            [0, true],
            [1, true],
            [0.1, true],
            [.1, true],
            ['0.0', true],
            ['.0', true],
            ['.1', true],
            ['', false],
            ['a', false],
            [' ', false],
        ];
    }

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
            ['0', true],
            ['0.1', false],
            ['', false],
            [' ', false],
            ['a', false],
            [0, true],
            [0.1, false],
            [.1, false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkIpProvider
     */
    public function testCheckIp($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkIp($value));
        } else {
            $this->assertFalse($this->validator()->checkIp($value));
        }
    }

    public function checkIpProvider()
    {
        return [
            ['127.0.0.1', true],
            ['200.100.1.1', true],
            ['255.255.255.255', true],
            ['0.0.0.0', true],
            ['1.1.1.1', true],
            ['254.254.254.254', true],
            ['127.0.0.256', false],
            ['256.0.0.1', false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkMacAddressProvider
     */
    public function testCheckMacAddress($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkMacAddress($value));
        } else {
            $this->assertFalse($this->validator()->checkMacAddress($value));
        }
    }

    public function checkMacAddressProvider()
    {
        return [
            ['a1:b6:7f:2d:f0:a7', true],
            ['a1:b6:7f:2z:f0:a7', false],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider checkUrlProvider
     */
    public function testCheckUrl($value, $expected)
    {
        if ($expected) {
            $this->assertTrue($this->validator()->checkUrl($value));
        } else {
            $this->assertFalse($this->validator()->checkUrl($value));
        }
    }

    public function checkUrlProvider()
    {
        return [
            ['http://paliari.com', true],
            ['https://paliari.com', true],
            ['http://paliari.com.br', true],
            ['https://paliari.com.br', true],
            ['http://paliari.com/a=1', true],
            ['paliari.com', false],
            ['com', false],
            ['paliari', false],
        ];
    }

    protected function validator()
    {
        return f::instance();
    }

}
