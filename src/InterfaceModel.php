<?php
namespace Paliari\Doctrine;

use Doctrine\ORM\EntityManager;

interface InterfaceModel
{

    /**
     * @return array
     */
    public static function getValidatesPresenceOf();

    /**
     * @return array
     */
    public static function getValidatesSizeOf();

    /**
     * @return array
     */
    public static function getValidatesLengthOf();

    /**
     * @return array
     */
    public static function getValidatesInclusionOf();

    /**
     * @return array
     */
    public static function getValidatesExclusionOf();

    /**
     * @return array
     */
    public static function getValidatesFormatOf();

    /**
     * @return array
     */
    public static function getValidatesNumericalityOf();

    /**
     * @return array
     */
    public static function getValidatesUniquenessOf();

    /**
     * @return array
     */
    public static function getValidatesCustom();


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

    public static function className();

}
