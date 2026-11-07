<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Paliari\Doctrine\ModelException;
use PHPUnit\Framework\TestCase;

class ModelValidatorEventSubscriberTest extends TestCase
{

    private ?EntityManager $previous_em = null;

    private EntityManager $em;

    public function setUp(): void
    {
        $this->previous_em = EM::getEm();
        $this->em          = createEntityManager();
        EM::setEm($this->em);
        (new SchemaTool($this->em))->createSchema([
            $this->em->getClassMetadata(MyModel::class),
            $this->em->getClassMetadata(CallbacksModel::class),
            $this->em->getClassMetadata(PlainEntity::class),
        ]);
    }

    public function tearDown(): void
    {
        EM::setEm($this->previous_em);
    }

    public function testPersistInvalidModelThrows()
    {
        $model        = $this->validModel();
        $model->email = 'invalid';
        try {
            $this->em->persist($model);
            $this->fail('ModelException not thrown');
        } catch (ModelException $e) {
            $this->assertArrayHasKey('email', $e->getValidatorErrors()->toArray());
        }
        $this->assertTrue($model->isNewRecord());
    }

    public function testPersistLoadAndUpdate()
    {
        $model = $this->validModel();
        $this->em->persist($model);
        $this->em->flush();
        $this->assertTrue($model->isUpdateRecord());
        $this->em->clear();

        $loaded = $this->em->find(MyModel::class, $model->id);
        $this->assertTrue($loaded->isUpdateRecord());
        $this->assertNull($loaded->errors);

        $loaded->email = 'invalid';
        $this->expectException(ModelException::class);
        $this->em->flush();
    }

    public function testUpdateInTheSameSessionIsValidated()
    {
        $model = $this->validModel();
        $this->em->persist($model);
        $this->em->flush();

        $model->email = 'invalid';
        $this->expectException(ModelException::class);
        $this->em->flush();
    }

    public function testErrorsAreResetAfterEachWrite()
    {
        $model = $this->validModel();
        $this->em->persist($model);
        $this->assertNotNull($model->errors);
        $this->em->flush();
        $this->assertNull($model->errors);

        $model->name = 'other name';
        $this->em->flush();
        $this->assertNull($model->errors);
        $this->assertTrue($model->isUpdateRecord());

        $this->em->remove($model);
        $this->assertNotNull($model->errors);
        $this->em->flush();
        $this->assertNull($model->errors);
        $this->assertTrue($model->isRemoveRecord());
    }

    public function testIsValidBeforePersistRunsCallbacksOnce()
    {
        $model       = new CallbacksModel();
        $model->name = 'name';
        $this->assertTrue($model->isValid());
        $this->em->persist($model);
        $this->em->flush();
        $this->assertSame(
            ['before_validation', 'before_validation_on_create', 'after_validation', 'after_validation_on_create'],
            $model->calls
        );
    }

    public function testRemove()
    {
        $model = $this->validModel();
        $this->em->persist($model);
        $this->em->flush();
        $id = $model->id;
        $this->em->remove($model);
        $this->assertTrue($model->isRemoveRecord());
        $this->em->flush();
        $this->em->clear();
        $this->assertNull($this->em->find(MyModel::class, $id));
    }

    public function testRemoveInvalidModel()
    {
        $model = $this->validModel();
        $this->em->persist($model);
        $this->em->flush();
        $id           = $model->id;
        $model->email = 'invalid';
        $this->em->remove($model);
        $this->em->flush();
        $this->em->clear();
        $this->assertNull($this->em->find(MyModel::class, $id));
    }

    public function testOnRemoveValidationBlocksRemove()
    {
        $model       = new CallbacksModel();
        $model->name = 'name';
        $this->em->persist($model);
        $this->em->flush();

        $model->block_remove = true;
        try {
            $this->em->remove($model);
            $this->fail('ModelException not thrown');
        } catch (ModelException $e) {
            $this->assertSame(['base' => ['cannot be removed']], $e->getValidatorErrors()->toArray());
        }
    }

    public function testEntitiesWithoutTheTraitAreIgnored()
    {
        $entity       = new PlainEntity();
        $entity->name = 'plain';
        $this->em->persist($entity);
        $this->em->flush();
        $id = $entity->id;
        $this->em->clear();

        $loaded       = $this->em->find(PlainEntity::class, $id);
        $loaded->name = 'changed';
        $this->em->flush();
        $this->em->clear();
        $this->assertSame('changed', $this->em->find(PlainEntity::class, $id)->name);

        $this->em->remove($this->em->find(PlainEntity::class, $id));
        $this->em->flush();
        $this->em->clear();
        $this->assertNull($this->em->find(PlainEntity::class, $id));
    }

    private function validModel(): MyModel
    {
        $model         = new MyModel();
        $model->email  = 'a@a.com';
        $model->name   = 'name';
        $model->active = true;
        $model->value  = 1.5;
        $model->times  = 1;

        return $model;
    }

}
