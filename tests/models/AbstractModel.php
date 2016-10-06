<?php

class AbstractModel implements \Paliari\Doctrine\InterfaceModel
{

    use \Paliari\Doctrine\TraitValidatorModel;

    public static function find($id)
    {
        return static::getEm()->find(get_called_class(), $id);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEm()
    {
        return EM::getEm();
    }

}
