<?php
use Paliari\Doctrine\Validator as V;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var V
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

    /**
     * @var ReflectionClass
     */
    private $reflection_model;

    public function setUp()
    {
        $this->model            = new MyModel();
        $this->validator        = new V($this->model);
        $this->reflection       = new ReflectionClass($this->validator);
        $this->reflection_model = new ReflectionClass($this->model);
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

    /**
     * @dataProvider dataProviderSkipValidation
     */
    public function testSkipValidation($field, $options, $values, $state, $expected)
    {
        foreach ($values as $k => $v) {
            $this->model->$k = $v;
        }
        $p = $this->reflection_model->getProperty('record_state');
        $p->setAccessible(true);
        $p->setValue($this->model, $state);
        $this->doTestMethod('skipValidation', [$field, $options], $expected);
    }

    public function dataProviderSkipValidation()
    {
        return [
            ['name', [], [], V::CREATE, false],
            ['name', ['on' => V::REMOVE], [], V::REMOVE, false],
            ['name', ['on' => V::REMOVE], [], V::CREATE, true],
            ['nike_name', ['if' => 'name'], ['name' => 'a'], V::CREATE, false],
            ['nike_name', ['if' => 'name'], ['name' => ''], V::CREATE, true],
            ['nike_name', ['unless' => 'name'], ['name' => 'a'], V::CREATE, true],
            ['nike_name', ['unless' => 'name'], ['name' => ''], V::CREATE, false],
            ['name', ['on' => V::CREATE], [], V::CREATE, false],
            ['name', ['on' => V::UPDATE], [], V::CREATE, true],
            ['name', ['on' => V::SAVE], [], V::CREATE, false],
            ['name', ['allow_nil' => true], ['name' => ''], V::CREATE, false],
            ['name', ['allow_nil' => true], ['name' => null], V::CREATE, true],
            ['name', ['allow_nil' => false], ['name' => null], V::CREATE, false],
            ['name', ['allow_blank' => true], ['name' => ''], V::CREATE, true],
            ['name', ['allow_blank' => true], ['name' => '   '], V::CREATE, true],
            ['name', ['allow_blank' => true], ['name' => null], V::CREATE, true],
            ['name', ['allow_blank' => true], ['name' => 'a'], V::CREATE, false],
            ['name', ['allow_blank' => false], ['name' => null], V::CREATE, false],
            ['name', ['allow_blank' => false], ['name' => 'a'], V::CREATE, false],
        ];
    }

    /**
     * @dataProvider dataProviderValidateOf
     */
    public function testValidateOf($method, $field, $options, $value, $expected)
    {
        $this->model->$field = $value;
        $this->validator->$method($field, $options);
        $this->assertEquals($expected, $this->model->errors->isValid());
    }

    public function dataProviderValidateOf()
    {
        return [
            ['presenceOf', 'name', [], 'a', true],
            ['presenceOf', 'name', [], '', false],
            ['lengthOf', 'name', ['minimum' => 1], 'aa', true],
            ['lengthOf', 'name', ['minimum' => 1], 'a', true],
            ['lengthOf', 'name', ['minimum' => 10], 'aa', false],
            ['lengthOf', 'name', ['maximum' => 10], 'aa', true],
            ['lengthOf', 'name', ['maximum' => 2], 'aa', true],
            ['lengthOf', 'name', ['maximum' => 2], 'aaa', false],
            ['lengthOf', 'name', ['is' => 2], 'aa', true],
            ['lengthOf', 'name', ['is' => 2], 'aaa', false],
            ['lengthOf', 'name', ['in' => [2, 4]], 'aaa', true],
            ['lengthOf', 'name', ['in' => [2, 4]], 'aaa', true],
            ['lengthOf', 'name', ['in' => [2, 4]], 'aa', true],
            ['lengthOf', 'name', ['in' => [2, 4]], 'aaaa', true],
            ['lengthOf', 'name', ['in' => [2, 4]], 'a', false],
            ['lengthOf', 'name', ['in' => [2, 4]], null, false],
            ['lengthOf', 'name', ['in' => [2, 4]], 'aaaaa', false],
            ['lengthOf', 'name', ['within' => [2, 4]], 'aaa', true],
            ['lengthOf', 'name', ['within' => [2, 4]], 'aaa', true],
            ['lengthOf', 'name', ['within' => [2, 4]], 'aa', true],
            ['lengthOf', 'name', ['within' => [2, 4]], 'aaaa', true],
            ['lengthOf', 'name', ['within' => [2, 4]], 'a', false],
            ['lengthOf', 'name', ['within' => [2, 4]], null, false],
            ['lengthOf', 'name', ['within' => [2, 4]], 'aaaaa', false],
            ['sizeOf', 'name', ['within' => [2, 4]], null, false],
            ['sizeOf', 'name', ['within' => [2, 4]], 'aaaaa', false],
            ['inclusionOf', 'name', ['within' => ['a']], 'a', true],
            ['inclusionOf', 'name', ['within' => ['b']], 'a', false],
            ['inclusionOf', 'name', ['in' => ['a']], 'a', true],
            ['inclusionOf', 'name', ['in' => ['b']], 'a', false],
            ['exclusionOf', 'name', ['in' => ['b']], 'a', true],
            ['exclusionOf', 'name', ['in' => ['a']], 'a', false],
            ['exclusionOf', 'name', ['within' => ['b']], 'a', true],
            ['exclusionOf', 'name', ['within' => ['a']], 'a', false],
            ['formatOf', 'email', ['with' => 'email'], 'aa@mail.com', true],
            ['formatOf', 'email', ['with' => 'email'], 'aa@.com', false],
            ['formatOf', 'name', ['with' => 'url'], 'http://paliari.com', true],
            ['formatOf', 'name', ['with' => 'url'], 'paliari.com', false],
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
