<?php

/**
 * @Entity
 * @Table(name="foo")
 */
class MyModel extends \AbstractModel
{

    protected static array $validates_format_of = [
        'email' => ['with' => 'email'],
    ];
    protected static array $validates_custom = ['count'];

    /**
     * Primary Key column.
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Column(type="string", length=100, unique=true, nullable=false)
     */
    public $email = '';

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    public $name = '';

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    public $nike_name = '';

    /**
     * @Column(type="boolean", nullable=false)
     */
    public $active;

    /**
     * @Column(type="float", nullable=false)
     */
    public $value;

    /**
     * @Column(type="integer", nullable=false)
     */
    public $times;

    /**
     * @Column(type="integer", nullable=true)
     */
    public $count;

    public function count()
    {
        if ($this->count > 10) {
            $this->errors->add('count', 'too long');
        }
    }

}
