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
        $method = $this->reflection->getMethod('isBlank');
        $method->setAccessible(true);
        $res = $method->invokeArgs($this->validator, [$value]);
        $this->assertEquals($expected, $res);
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

}
