<?php

class AbstractModel implements \Paliari\Doctrine\ModelValidatorInterface
{

    use \Paliari\Doctrine\TraitValidatorModel;

    public static function find($id)
    {
        return static::getEm()->find(get_called_class(), $id);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEM()
    {
        return EM::getEm();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function humAttribute(string $name): string
    {
        return ucwords($name);
    }
}
