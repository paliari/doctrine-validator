<?php

use Paliari\Doctrine\ModelException;
use Paliari\Doctrine\Validator;
use PHPUnit\Framework\TestCase;

class TraitValidatorModelTest extends TestCase
{

    use ResetsStaticState;

    public function tearDown(): void
    {
        $this->restoreStatics();
        $this->resetValidatorCaches();
    }

    public function testCallbacksOnCreate()
    {
        $model = $this->model();
        $model->validate();
        $this->assertSame(
            ['before_validation', 'before_validation_on_create', 'after_validation', 'after_validation_on_create'],
            $model->calls
        );
    }

    public function testCallbacksOnUpdate()
    {
        $model = $this->model();
        $model->setRecordState(Validator::UPDATE);
        $model->validate();
        $this->assertSame(
            ['before_validation', 'before_validation_on_update', 'after_validation', 'after_validation_on_update'],
            $model->calls
        );
    }

    public function testCallbacksOnRemoveSkipValidationsWithoutOnRemove()
    {
        $model       = $this->model();
        $model->name = null;
        $model->setRecordState(Validator::REMOVE);
        $model->validate();
        $this->assertSame(
            ['before_validation', 'before_validation_on_remove', 'after_validation', 'after_validation_on_remove'],
            $model->calls
        );
    }

    public function testAfterCallbacksAreNotCalledWhenInvalid()
    {
        $model       = $this->model();
        $model->name = null;
        try {
            $model->validate();
            $this->fail('ModelException not thrown');
        } catch (ModelException $e) {
            $this->assertSame(['name' => ["can't be blank"]], $e->getValidatorErrors()->toArray());
        }
        $this->assertSame(['before_validation', 'before_validation_on_create'], $model->calls);
    }

    public function testOnRemoveValidation()
    {
        $model               = $this->model();
        $model->block_remove = true;
        $this->assertTrue($model->isValid());

        $model               = $this->model();
        $model->block_remove = true;
        $model->setRecordState(Validator::REMOVE);
        $this->assertFalse($model->isValid());
        $this->assertSame(['base' => ['cannot be removed']], $model->errors->toArray());
    }

    public function testIsValid()
    {
        $this->assertTrue($this->model()->isValid());
        $model       = $this->model();
        $model->name = '';
        $this->assertFalse($model->isValid());
    }

    public function testIsValidThrow()
    {
        $model       = $this->model();
        $model->name = '';
        $this->expectException(ModelException::class);
        $model->isValid(true);
    }

    public function testIsValidRethrowsOtherExceptions()
    {
        $model                  = $this->model();
        $model->throw_on_before = new RuntimeException('callback failed');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('callback failed');
        $model->isValid();
    }

    public function testRecordState()
    {
        $model = new CallbacksModel();
        $this->assertSame(Validator::CREATE, $model->recordState());
        $this->assertTrue($model->isNewRecord());
        $this->assertFalse($model->isUpdateRecord());
        $this->assertFalse($model->isRemoveRecord());

        $model->setRecordState(Validator::UPDATE);
        $this->assertSame(Validator::UPDATE, $model->recordState());
        $this->assertTrue($model->isUpdateRecord());
        $this->assertFalse($model->isNewRecord());

        $model->setRecordState(Validator::REMOVE);
        $this->assertTrue($model->isRemoveRecord());
        $this->assertFalse($model->isNewRecord());
        $this->assertFalse($model->isUpdateRecord());

        $model->setRecordState(Validator::CREATE);
        $this->assertTrue($model->isNewRecord());
    }

    public function testGetValidatesAndClassName()
    {
        $this->assertSame(['name'], CallbacksModel::getValidates('validates_presence_of'));
        $this->assertSame([], CallbacksModel::getValidates('validates_format_of'));
        $this->assertSame('CallbacksModel', CallbacksModel::className());
    }

    public function testAddCustomValidator()
    {
        $this->backupStatic(CallbacksModel::class, 'validates_custom');
        $this->resetValidatorCaches();
        CallbacksModel::addCustomValidator(function ($model) {
            if ('forbidden' === $model->name) {
                $model->errors->add('name', 'is forbidden');
            }
        });

        $this->assertTrue($this->model()->isValid());

        $model       = $this->model();
        $model->name = 'forbidden';
        $this->assertFalse($model->isValid());
        $this->assertSame(['name' => ['is forbidden']], $model->errors->toArray());
    }

    public function testValidateReusesPreviousResultUntilErrorsAreReset()
    {
        $model       = $this->model();
        $model->name = '';
        $this->assertFalse($model->isValid());

        $model->name = 'fixed';
        $this->assertFalse($model->isValid());

        $model->errors = null;
        $this->assertTrue($model->isValid());

        $model->name = '';
        $this->assertTrue($model->isValid());
    }

    private function model(): CallbacksModel
    {
        $model       = new CallbacksModel();
        $model->name = 'name';

        return $model;
    }

}
