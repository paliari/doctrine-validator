<?php
use Paliari\Doctrine\Validators\ModelCustomValidator,
    PHPUnit\Framework\TestCase;

class ModelCustomValidatorTest extends TestCase
{

    use ResetsStaticState;

    public function setUp(): void
    {
        $this->backupStatic(MyModel::class, 'validates_custom');
    }

    public function tearDown(): void
    {
        $this->restoreStatics();
        $this->resetValidatorCaches();
    }

    public function testAddRun()
    {
        $nfe  = new MyModel();
        $mock = $this->mock();
        $mock->expects($this->once())
             ->method('exec')
             ->with($nfe)
        ;
        ModelCustomValidator::i()->add('a', [$mock, 'exec']);
        ModelCustomValidator::i()->run('a', $nfe);
    }

    public function testByModels()
    {
        $model = new MyModel();
        $mock  = $this->mock();
        $mock->expects($this->once())
             ->method('exec')
             ->with($model)
        ;
        MyModel::addCustomValidator([$mock, 'exec']);
        $this->assertArrayHasKey('validateModelCustom', MyModel::getValidates('validates_custom'));
        ModelCustomValidator::i()->run(MyModel::className(), $model);
    }

    public function testRunsEveryValidatorInOrder()
    {
        $calls = [];
        ModelCustomValidator::i()
            ->add('b', function () use (&$calls) {
                $calls[] = 1;
            })
            ->add('b', function () use (&$calls) {
                $calls[] = 2;
            })
        ;
        ModelCustomValidator::i()->run('b', new MyModel());
        $this->assertSame([1, 2], $calls);
    }

    public function testRunWithoutValidators()
    {
        $this->expectNotToPerformAssertions();
        ModelCustomValidator::i()->run('without-validators', new MyModel());
    }

    protected function mock()
    {
        return $this->getMockBuilder(CallValidatorModelCustomValidator::class)
                    ->onlyMethods(['exec'])
                    ->getMock()
            ;
    }

}

class CallValidatorModelCustomValidator
{
    public function exec($model)
    {
    }
}
