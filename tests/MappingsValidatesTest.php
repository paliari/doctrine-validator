<?php

use Paliari\Doctrine\MappingsValidates;
use PHPUnit\Framework\Attributes\DataProvider;

class MappingsValidatesTest extends \PHPUnit\Framework\TestCase
{

    #[DataProvider('defaultsFieldsDataProvider')]
    public function testFieldsGetDefaults($model_name, $validation_name, $field, $expected)
    {
        $validation = MappingsValidates::getDefaults($model_name, $validation_name);
        $this->assertEquals($expected, $validation[$field]);
    }

    public static function defaultsFieldsDataProvider()
    {
        return [
            ['MyModel', 'validates_length_of', 'email', ['maximum' => 100, 'allow_blank' => false]],
            ['MyModel', 'validates_length_of', 'name', ['maximum' => 255, 'allow_blank' => false]],
            ['MyModel', 'validates_length_of', 'nike_name', ['maximum' => 255, 'allow_blank' => true]],
            ['MyModel', 'validates_numericality_of', 'value', ['only_integer' => false, 'allow_blank' => false]],
            ['MyModel', 'validates_numericality_of', 'times', ['only_integer' => true, 'allow_blank' => false]],
            ['MyModel', 'validates_numericality_of', 'count', ['only_integer' => true, 'allow_blank' => true]],
            ['MyModel', 'validates_inclusion_of', 'active', ['in' => [true, false], 'allow_blank' => false]],
            ['TypesModel', 'validates_length_of', 'code', ['maximum' => 5, 'allow_blank' => false]],
            ['TypesModel', 'validates_numericality_of', 'price', ['only_integer' => false, 'allow_blank' => false]],
            ['TypesModel', 'validates_numericality_of', 'big', ['only_integer' => true, 'allow_blank' => true]],
            ['TypesModel', 'validates_numericality_of', 'small', ['only_integer' => true, 'allow_blank' => false]],
            ['TypesModel', 'validates_inclusion_of', 'flag', ['in' => [true, false], 'allow_blank' => true]],
        ];
    }

    #[DataProvider('fieldsWithoutDefaultsDataProvider')]
    public function testFieldsWithoutDefaults($model_name, $field)
    {
        foreach (['validates_length_of', 'validates_numericality_of', 'validates_inclusion_of'] as $validation_name) {
            $this->assertArrayNotHasKey($field, MappingsValidates::getDefaults($model_name, $validation_name));
        }
    }

    public static function fieldsWithoutDefaultsDataProvider()
    {
        return [
            ['MyModel', 'id'],
            ['TypesModel', 'id'],
            ['TypesModel', 'label'],
            ['TypesModel', 'description'],
        ];
    }

}
