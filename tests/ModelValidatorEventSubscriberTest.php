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
        (new SchemaTool($this->em))->createSchema([$this->em->getClassMetadata(MyModel::class)]);
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

    public function testRemove()
    {
        $model = $this->validModel();
        $this->em->persist($model);
        $this->em->flush();
        $id            = $model->id;
        $model->errors = null;
        $this->em->remove($model);
        $this->assertTrue($model->isRemoveRecord());
        $this->em->flush();
        $this->em->clear();
        $this->assertNull($this->em->find(MyModel::class, $id));
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
