<?php

class ValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Paliari\Doctrine\Validator
     */
    private $validator;

    /**
     * @var MyModel
     */
    private $model;

    /**
     * @var ReflectionClass
     */
    private $reflection;

    public function setUp()
    {
        $this->model      = new MyModel();
        $this->validator  = new \Paliari\Doctrine\Validator($this->model);
        $this->reflection = new ReflectionClass($this->validator);
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider dataProviderIsBlank
     */
    public function testIsBlank($value, $expected)
    {
        $this->doTestMethod('isBlank', [$value], $expected);
    }

    public function dataProviderIsBlank()
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
     *
     * @dataProvider dataProviderIsInteger
     */
    public function testIsInteger($value, $expected)
    {
        $this->doTestMethod('isInteger', [$value], $expected);
    }

    public function dataProviderIsInteger()
    {
        return [
            ['.', false],
            ['a', false],
            ['a1', false],
            ['1a', false],
            ['1e', false],
            ['0001', false],
            [9999999999999999999, false],
            [999999999999999999, true],
            ['0', true],
            ['1', true],
            ['999', true],
            [1, true],
            [0, true],
            [00001, true],
            [00000, true],
        ];
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     *
     * @dataProvider dataProviderIsNumber
     */
    public function testIsNumber($value, $expected)
    {
        $this->doTestMethod('isNumber', [$value], $expected);
    }

    public function dataProviderIsNumber()
    {
        return [
            ['.', false],
            ['a', false],
            ['a1', false],
            ['1a', false],
            ['', false],
            ['0001', true],
            [0.0, true],
            [1.0000, true],
            ['0', true],
            ['0.0', true],
            ['1', true],
            ['999', true],
            [1, true],
            [0, true],
            [00001, true],
            [00000, true],
        ];
    }

    /**
     * @dataProvider dataProviderCheckFilterVar
     */
    public function testCheckFilterVar($value, $filter, $expected)
    {
        $this->doTestMethod('checkFilterVar', [$value, $filter], $expected);
    }

    public function dataProviderCheckFilterVar()
    {
        return [
            [true, 'boolean', true],
            [false, 'boolean', true],
            ['false', 'boolean', true],
            ['true', 'boolean', true],
            ['on', 'boolean', true],
            ['off', 'boolean', true],
            [null, 'boolean', true],
            [[], 'boolean', false],
            ['aa@aaa.aa', 'email', true],
            ['aa@', 'email', false],
            ['aa@.a', 'email', false],
            ['aa@a..a', 'email', false],
            ['0.0', 'float', true],
            ['', 'float', false],
            ['0', 'integer', true],
            [0, 'integer', true],
            ['127.0.0.1', 'ip', true],
            ['127.0.0.256', 'ip', false],
            ['http://paliari.com', 'url', true],
            ['paliari.com', 'url', false],
        ];
    }

    /**
     * @dataProvider dataProviderComparatorThan
     */
    public function testComparatorThan($comparator, $value, $than, $expected)
    {
        $this->doTestMethod('comparatorThan', [$comparator, $value, $than], $expected);
    }

    public function dataProviderComparatorThan()
    {
        return [
            ['greater_than', 2, 1, true],
            ['greater_than', 1, 2, false],
            ['greater_than', 1, 1, false],
            ['greater_than_or_equal_to', 2, 1, true],
            ['greater_than_or_equal_to', 1, 2, false],
            ['greater_than_or_equal_to', 1, 1, true],
            ['less_than', 2, 1, false],
            ['less_than', 1, 2, true],
            ['less_than', 1, 1, false],
            ['less_than_or_equal_to', 2, 1, false],
            ['less_than_or_equal_to', 1, 2, true],
            ['less_than_or_equal_to', 1, 1, true],
            ['equal_to', 1, 1, true],
            ['equal_to', 2, 1, false],
            ['other_than', 1, 1, false],
            ['other_than', 2, 1, true],
        ];
    }

    public function doTestMethod($method, $args, $expected)
    {
        $res = $this->invokeProtectedMethod($method, $args);
        $this->assertEquals($expected, $res);
    }

    public function invokeProtectedMethod($method, $args)
    {
        $method = $this->reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($this->validator, $args);
    }

}
