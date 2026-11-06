<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidatorErrorsTest extends TestCase
{

    /**
     * @var \Paliari\Doctrine\ValidatorErrors
     */
    private $error;

    public function setUp(): void
    {
        $this->error = new \Paliari\Doctrine\ValidatorErrors();
    }

    public function testGetDefaultMessage()
    {
        $this->assertSame('is invalid', \Paliari\Doctrine\ValidatorErrors::getDefaultMessage('invalid'));
        $this->assertNull(\Paliari\Doctrine\ValidatorErrors::getDefaultMessage('other_than'));
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
