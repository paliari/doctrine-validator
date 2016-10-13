<?php
namespace Paliari\Doctrine;

use Doctrine\ORM\EntityManager;

interface InterfaceModel
{

    /**
     * @return array
     */
    public static function getValidates($name);

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @return bool
     */
    public function isNewRecord();

    /**
     * @return string
     */
    public function recordState();

    /**
     * @return EntityManager
     */
    public static function getEm();

    /**
     * @return string
     */
    public static function className();

}
