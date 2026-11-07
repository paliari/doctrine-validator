<?php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'callbacks_model')]
class CallbacksModel extends \AbstractModel
{

    protected static array $before_validation = ['beforeValidation'];
    protected static array $after_validation = ['afterValidation'];
    protected static array $before_validation_on_create = ['beforeValidationOnCreate'];
    protected static array $after_validation_on_create = ['afterValidationOnCreate'];
    protected static array $before_validation_on_update = ['beforeValidationOnUpdate'];
    protected static array $after_validation_on_update = ['afterValidationOnUpdate'];
    protected static array $before_validation_on_remove = ['beforeValidationOnRemove'];
    protected static array $after_validation_on_remove = ['afterValidationOnRemove'];

    protected static array $validates_presence_of = ['name'];
    protected static array $validates_custom = [
        'checkRemove' => ['on' => 'remove'],
    ];

    public array $calls = [];

    public bool $block_remove = false;

    public ?Throwable $throw_on_before = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    public $name;

    public function beforeValidation()
    {
        if ($this->throw_on_before) {
            throw $this->throw_on_before;
        }
        $this->calls[] = 'before_validation';
    }

    public function afterValidation()
    {
        $this->calls[] = 'after_validation';
    }

    public function beforeValidationOnCreate()
    {
        $this->calls[] = 'before_validation_on_create';
    }

    public function afterValidationOnCreate()
    {
        $this->calls[] = 'after_validation_on_create';
    }

    public function beforeValidationOnUpdate()
    {
        $this->calls[] = 'before_validation_on_update';
    }

    public function afterValidationOnUpdate()
    {
        $this->calls[] = 'after_validation_on_update';
    }

    public function beforeValidationOnRemove()
    {
        $this->calls[] = 'before_validation_on_remove';
    }

    public function afterValidationOnRemove()
    {
        $this->calls[] = 'after_validation_on_remove';
    }

    public function checkRemove()
    {
        if ($this->block_remove) {
            $this->errors->add('base', 'cannot be removed');
        }
    }

}
