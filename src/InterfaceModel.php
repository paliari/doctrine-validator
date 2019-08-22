<?php

namespace Paliari\Doctrine;

interface InterfaceModel
{

    /**
     * @param string $name
     *
     * @return array
     */
    public static function getValidates($name);

    /**
     * @param $throw
     *
     * @return bool
     */
    public function isValid($throw = false);

    /**
     * @return bool
     */
    public function isNewRecord();

    /**
     * @return string
     */
    public function recordState();

    /**
     * @return string
     */
    public static function className();

}
