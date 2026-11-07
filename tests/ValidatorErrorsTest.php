<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidatorErrorsTest extends TestCase
{

    use ResetsStaticState;

    /**
     * @var \Paliari\Doctrine\ValidatorErrors
     */
    private $error;

    public function setUp(): void
    {
        $this->backupStatic(\Paliari\Doctrine\ValidatorErrors::class, 'custom_get_message');
        $this->error = new \Paliari\Doctrine\ValidatorErrors();
    }

    public function tearDown(): void
    {
        $this->restoreStatics();
    }

    public function testGetDefaultMessage()
    {
        $this->assertSame('is invalid', \Paliari\Doctrine\ValidatorErrors::getDefaultMessage('invalid'));
        $this->assertNull(\Paliari\Doctrine\ValidatorErrors::getDefaultMessage('other_than'));
    }

    public function testCustomGetMessage()
    {
        \Paliari\Doctrine\ValidatorErrors::$custom_get_message = fn($key) => "custom $key";
        $this->assertSame('custom blank', \Paliari\Doctrine\ValidatorErrors::getDefaultMessage('blank'));
        $this->error->add('name', '');
        $this->assertSame(['name' => ['custom invalid']], $this->error->toArray());
    }

    public function testCustomGetMessageInValidator()
    {
        \Paliari\Doctrine\ValidatorErrors::$custom_get_message = fn($key) => "não pode ficar em branco ($key)";
        $model = new MyModel();
        (new \Paliari\Doctrine\Validator($model))->presenceOf('name', []);
        $this->assertSame(['name' => ['não pode ficar em branco (blank)']], $model->errors->toArray());
    }

    public function testAddEmptyMessageUsesInvalid()
    {
        $this->error->add('name', '');
        $this->assertSame(['name' => ['is invalid']], $this->error->toArray());
    }

    public function testAsJson()
    {
        $this->error->model_name = MyModel::class;
        $this->error->add('name', "can't be blank");
        $this->assertSame(['my_model' => ['name' => ["can't be blank"]]], $this->error->asJson());
    }

    public function testToString()
    {
        $this->error->model_name = MyModel::class;
        $this->error->add('name', "can't be blank");
        $this->error->add('name', 'is too short');
        $this->error->add('nike_name', 'is invalid');
        $this->assertSame("Name: can't be blank, is too short\nNike_name: is invalid\n", (string)$this->error);
    }

    public function testIsValid()
    {
        $this->assertTrue($this->error->isValid());
        $this->error->add('b', 'fail');
        $this->assertFalse($this->error->isValid());
    }

    #[DataProvider('addDataProvider')]
    public function testAdd($name, $message)
    {
        $this->error->add($name, $message);
        $a = $this->error->toArray();
        $this->assertEquals($a[$name][0], $message);
    }

    public static function addDataProvider()
    {
        return [
            ['name', 'invalid'],
            ['a', 'a'],
        ];
    }

    #[DataProvider('addCountDataProvider')]
    public function testAddCount($name, $messages, $count)
    {
        foreach ($messages as $message) {
            $this->error->add($name, $message);
        }
        $a = $this->error->toArray();
        $this->assertEquals($a[$name], $messages);
        $this->assertEquals(count($a[$name]), $count);
    }

    public static function addCountDataProvider()
    {
        return [
            ['name', ['invalid'], 1],
            ['a', ['a', 'b'], 2],
        ];
    }

}
