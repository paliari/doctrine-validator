<?php
use Paliari\Doctrine\Validators\ModelCustomValidator,
    PHPUnit\Framework\TestCase;

class ModelCustomValidatorTest extends TestCase
{

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
        ModelCustomValidator::i()->run(MyModel::className(), $model);
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
