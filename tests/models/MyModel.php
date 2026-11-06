<?php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'foo')]
class MyModel extends \AbstractModel
{

    protected static array $validates_format_of = [
        'email' => ['with' => 'email'],
    ];
    protected static array $validates_custom = ['count'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 100, unique: true, nullable: false)]
    public $email = '';

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    public $name = '';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public $nike_name = '';

    #[ORM\Column(type: 'boolean', nullable: false)]
    public $active;

    #[ORM\Column(type: 'float', nullable: false)]
    public $value;

    #[ORM\Column(type: 'integer', nullable: false)]
    public $times;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $count;

    public function count()
    {
        if ($this->count > 10) {
            $this->errors->add('count', 'too long');
        }
    }

}
