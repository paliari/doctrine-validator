<?php
use PHPUnit\Framework\TestCase;

class ValidatorErrorsTest extends TestCase
{

    /**
     * @var \Paliari\Doctrine\ValidatorErrors
     */
    private $error;

    public function setUp()
    {
        $this->error = new \Paliari\Doctrine\ValidatorErrors();
    }

    public function testIsValid()
    {
        $this->assertTrue($this->error->isValid());
        $this->error->add('b', 'fail');
        $this->assertFalse($this->error->isValid());
    }

    /**
     * @dataProvider addDataProvider
     */
    public function testAdd($name, $message)
    {
        $this->error->add($name, $message);
        $a = $this->error->toArray();
        $this->assertEquals($a[$name][0], $message);
    }

    public function addDataProvider()
    {
        return [
            ['name', 'invalid'],
            ['a', 'a'],
        ];
    }

    /**
     * @dataProvider addCountDataProvider
     */
    public function testAddCount($name, $messages, $count)
    {
        foreach ($messages as $message) {
            $this->error->add($name, $message);
        }
        $a = $this->error->toArray();
        $this->assertEquals($a[$name], $messages);
        $this->assertEquals(count($a[$name]), $count);
    }

    public function addCountDataProvider()
    {
        return [
            ['name', ['invalid'], 1],
            ['a', ['a', 'b'], 2],
        ];
    }

}
