<?php

use Paliari\Doctrine\ModelException;
use Paliari\Doctrine\ValidatorErrors;
use PHPUnit\Framework\TestCase;

class ModelExceptionTest extends TestCase
{

    public function testWithValidatorErrors()
    {
        $errors             = new ValidatorErrors();
        $errors->model_name = MyModel::class;
        $errors->add('name', "can't be blank");
        $e = new ModelException($errors, 0, null);
        $this->assertSame($errors, $e->getValidatorErrors());
        $this->assertSame("Name: can't be blank\n", $e->getMessage());
        $this->assertNull($e->getPrevious());
    }

    public function testWithPrevious()
    {
        $previous = new Exception('previous');
        $e        = new ModelException('message', 1, $previous);
        $this->assertSame('message', $e->getMessage());
        $this->assertSame(1, $e->getCode());
        $this->assertSame($previous, $e->getPrevious());
    }

}
