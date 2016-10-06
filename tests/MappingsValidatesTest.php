<?php

class MappingsValidatesTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider defaultsFieldsDataProvider
     */
    public function testFieldsGetDefaults($model_name, $validation_name, $field, $expected)
    {
        $validation = \Paliari\Doctrine\MappingsValidates::getDefaults($model_name, $validation_name);
        $this->assertEquals($expected, $validation[$field]);
    }

    public function defaultsFieldsDataProvider()
    {
        return [
            ['MyModel', 'validates_length_of', 'email', ['maximum' => 100, 'allow_blank' => false]],
            ['MyModel', 'validates_length_of', 'name', ['maximum' => 255, 'allow_blank' => false]],
            ['MyModel', 'validates_length_of', 'nike_name', ['maximum' => 255, 'allow_blank' => true]],
            ['MyModel', 'validates_numericality_of', 'value', ['only_integer' => false, 'allow_blank' => false]],
            ['MyModel', 'validates_numericality_of', 'times', ['only_integer' => true, 'allow_blank' => false]],
            ['MyModel', 'validates_numericality_of', 'count', ['only_integer' => true, 'allow_blank' => true]],
            ['MyModel', 'validates_inclusion_of', 'active', ['in' => [true, false], 'allow_blank' => false]],
        ];
    }

}
