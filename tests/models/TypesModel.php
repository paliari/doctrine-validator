<?php

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'types_model')]
class TypesModel extends \AbstractModel
{

    protected static array $validates_length_of = [
        'code' => ['minimum' => 3],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public $id;

    #[ORM\Column(type: 'string', length: 5, nullable: false)]
    public $code = '';

    #[ORM\Column(type: 'string', nullable: true)]
    public $label;

    #[ORM\Column(type: 'text', nullable: true)]
    public $description;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    public $price;

    #[ORM\Column(type: 'bigint', nullable: true)]
    public $big;

    #[ORM\Column(type: 'smallint', nullable: false)]
    public $small;

    #[ORM\Column(type: 'boolean', nullable: true)]
    public $flag;

}
