<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
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

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    public function setUp(): void
    {
        $this->em               = EM::getEm();
        $this->model            = new MyModel();
        $this->validator        = new V($this->model);
        $this->reflection       = new ReflectionClass($this->validator);
        $this->reflection_model = new ReflectionClass($this->model);
    }

    public function tearDown(): void
    {
        EM::setEm($this->em);
        $this->reflection->getProperty('_validates')->setValue(null, []);
    }

    /**
     * @param mixed $value
     * @param bool  $expected
     */
    #[DataProvider('dataProviderIsBlank')]
    public function testIsBlank($value, $expected)
    {
        $this->doTestMethod('isBlank', [$value], $expected);
    }

    public static function dataProviderIsBlank()
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
    #[DataProvider('dataProviderIsInteger')]
    public function testIsInteger($value, $expected)
    {
        $this->doTestMethod('isInteger', [$value], $expected);
    }

    public static function dataProviderIsInteger()
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
     */
    #[DataProvider('dataProviderIsNumber')]
    public function testIsNumber($value, $expected)
    {
        $this->doTestMethod('isNumber', [$value], $expected);
    }

    public static function dataProviderIsNumber()
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

    #[DataProvider('dataProviderCheckFilterVar')]
    public function testCheckFilterVar($value, $filter, $expected)
    {
        $this->doTestMethod('checkFilterVar', [$value, $filter], $expected);
    }

    public static function dataProviderCheckFilterVar()
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

    #[DataProvider('dataProviderComparatorThan')]
    public function testComparatorThan($comparator, $value, $than, $expected)
    {
        $this->doTestMethod('comparatorThan', [$comparator, $value, $than], $expected);
    }

    public static function dataProviderComparatorThan()
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

    #[DataProvider('dataProviderSkipValidation')]
    public function testSkipValidation($field, $options, $values, $state, $expected)
    {
        foreach ($values as $k => $v) {
            $this->model->$k = $v;
        }
        $this->reflection_model->getProperty('record_state')->setValue($this->model, $state);
        $this->doTestMethod('skipValidation', [$field, $options], $expected);
    }

    public static function dataProviderSkipValidation()
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

    #[DataProvider('dataProviderValidateOf')]
    public function testValidateOf($method, $field, $options, $value, $expected)
    {
        $this->model->$field = $value;
        $this->validator->$method($field, $options);
        $this->assertEquals($expected, $this->model->errors->isValid());
    }

    public static function dataProviderValidateOf()
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
            ['formatOf', 'name', ['with' => '/\d/'], '001', true],
            ['formatOf', 'name', ['with' => '/\d/'], 'a', false],
            ['formatOf', 'name', ['with' => '/\D/'], '99', false],
            ['formatOf', 'name', ['with' => '/\D/'], 'aa', true],
            ['formatOf', 'name', ['without' => 'url'], 'adfasdsa', true],
            ['formatOf', 'name', ['with' => '/\d/'], null, false],
            ['formatOf', 'name', ['with' => '/^$/'], null, true],
            ['numericalityOf', 'value', ['only_integer' => true], 123, true],
            ['numericalityOf', 'value', ['only_integer' => true], '123', true],
            ['numericalityOf', 'value', ['only_integer' => true], '123.0', false],
            ['numericalityOf', 'value', ['only_integer' => true], 123.1, false],
            ['numericalityOf', 'value', [], 123.1, true],
            ['numericalityOf', 'value', [], 123, true],
            ['numericalityOf', 'value', [], 1.111111111111, true],
            ['numericalityOf', 'value', [], '1.111111111111', true],
            ['numericalityOf', 'value', [], '11a', false],
            ['numericalityOf', 'value', [], '', false],
            ['numericalityOf', 'value', [], null, false],
            ['custom', 'count', [], 0, true],
            ['custom', 'count', [], 20, false],
        ];
    }

    #[DataProvider('dataProviderMessages')]
    public function testMessages($method, $field, $options, $value, $expected)
    {
        $this->model->$field = $value;
        $this->validator->$method($field, $options);
        $this->assertSame([$field => $expected], $this->model->errors->toArray());
    }

    public static function dataProviderMessages()
    {
        return [
            ['numericalityOf', 'value', ['other_than' => 0], 0, ['is invalid']],
            ['numericalityOf', 'value', ['other_than' => 1], 1, ['is invalid']],
            ['numericalityOf', 'value', ['other_than' => 0, 'message' => 'custom'], 0, ['custom']],
            ['numericalityOf', 'value', ['greater_than' => 5, 'message' => 'custom %{count}'], 1, ['custom 5']],
            ['numericalityOf', 'value', ['greater_than' => 5], 1, ['must be greater than 5']],
            ['numericalityOf', 'value', ['greater_than' => 0], 0, ['must be greater than 0']],
            ['numericalityOf', 'value', ['less_than' => 5], 10, ['must be less than 5']],
            ['numericalityOf', 'value', ['equal_to' => 5], 1, ['must be equal to 5']],
            ['numericalityOf', 'value', ['not_a_number' => 'custom nan'], 'a', ['custom nan']],
            ['numericalityOf', 'value', ['greater_than_or_equal_to' => 0], -1, ['must be greater than or equal to 0']],
            ['numericalityOf', 'value', ['less_than' => 0], 1, ['must be less than 0']],
            ['numericalityOf', 'value', ['less_than_or_equal_to' => 0], 1, ['must be less than or equal to 0']],
            ['numericalityOf', 'value', ['equal_to' => 0], 1, ['must be equal to 0']],
            ['lengthOf', 'name', ['maximum' => 2], 'abc', ['is too long (maximum is 2 characters)']],
            ['lengthOf', 'name', ['is' => 0], 'a', ['is the wrong length (should be 0 characters)']],
        ];
    }

    #[DataProvider('dataProviderUniquenessOf')]
    public function testUniquenessOf($field, $options, $filters, $return, $expected)
    {
        $er = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
                   ->disableOriginalConstructor()->onlyMethods(['findBy'])->getMock()
        ;
        $er->expects($this->once())->method('findBy')
           ->with($filters)
           ->willReturn($return)
        ;
        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                   ->disableOriginalConstructor()->onlyMethods(['getRepository'])->getMock()
        ;
        $em->expects($this->once())->method('getRepository')->with('MyModel')->willReturn($er);
        EM::setEm($em);
        $this->validator->uniquenessOf($field, $options);
        $this->assertEquals($expected, $this->model->errors->isValid());
    }

    public static function dataProviderUniquenessOf()
    {
        $old_model     = new MyModel();
        $old_model->id = 1;

        return [
            ['email', ['scope' => ['name']], ['email' => '', 'name' => ''], [$old_model], false],
            ['email', [], ['email' => ''], [$old_model], false],
            ['email', ['scope' => ['name']], ['email' => '', 'name' => ''], [], true],
            ['email', [], ['email' => ''], [], true],
        ];
    }

    #[DataProvider('dataAllValidatesOf')]
    #[Group('a')]
    public function testAllValidatesOf($validate_of, $options, $values, $expected)
    {
        $property = $this->reflection->getProperty('_validates');
        $property->setValue(null, [$this->model->className() => [$validate_of => $options]]);
        foreach ($values as $field => $value) {
            $this->model->$field = $value;
        }
        $this->validator->validate();
        $this->assertEquals($expected, $this->model->errors->isValid());
        foreach ($options as $k => $option) {
            if (is_string($option)) {
                $k = $option;
            }
            $this->assertEquals($expected, !isset($this->model->errors->toArray()[$k]));
        }
        $this->assertEquals($expected, $this->model->errors->isValid());
    }

    public static function dataAllValidatesOf()
    {
        return [
            ['validates_presence_of', ['name'], [], false],
            ['validates_presence_of', ['name' => []], ['name' => 'a'], true],
            ['validates_size_of', ['name' => ['in' => [1, 3]]], ['name' => 'a'], true],
            ['validates_size_of', ['name' => ['in' => [1, 3]]], ['name' => ''], false],
            ['validates_length_of', ['name' => ['in' => [1, 3]]], ['name' => ''], false],
            ['validates_inclusion_of', ['name' => ['in' => ['a']]], [], false],
            ['validates_inclusion_of', ['name' => ['in' => ['a']]], ['name' => 'a'], true],
            ['validates_exclusion_of', ['name' => ['in' => ['a']]], [], true],
            ['validates_exclusion_of', ['name' => ['in' => ['a']]], ['name' => 'a'], false],
            ['validates_format_of', ['email' => ['with' => 'email']], ['email' => 'aaa@aa.aa'], true],
            ['validates_format_of', ['email' => ['with' => 'email']], [], false],
            ['validates_format_of', ['email' => ['with' => 'email', 'allow_blank' => true]], [], true],
            ['validates_format_of', ['email' => ['with' => 'email', 'allow_blank' => true]], ['email' => 'aa'], false],
            ['validates_numericality_of', ['value'], ['value' => '0'], true],
            ['validates_numericality_of', ['value'], ['value' => '1.02'], true],
            ['validates_numericality_of', ['value'], ['value' => 10.1], true],
            ['validates_numericality_of', ['value'], ['value' => ''], false],
            ['validates_numericality_of', ['value'], ['value' => 'a'], false],
            ['validates_numericality_of', ['value' => ['only_integer' => true]], ['value' => '0.1'], false],
            ['validates_numericality_of', ['value' => ['only_integer' => true]], ['value' => 0], true],
            ['validates_custom', ['count'], [], true],
            ['validates_custom', ['count'], ['count' => 20], false],
        ];
    }

    public function doTestMethod($method, $args, $expected)
    {
        $res = $this->invokeProtectedMethod($method, $args);
        $this->assertEquals($expected, $res);
    }

    public function invokeProtectedMethod($method, $args)
    {
        return $this->reflection->getMethod($method)->invokeArgs($this->validator, $args);
    }

}
