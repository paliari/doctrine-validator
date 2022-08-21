<?php

namespace Paliari\Doctrine;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractValidatorModel
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 *
 * @package Paliari\Doctrine
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractValidatorModel implements ModelValidatorInterface
{
    use TraitValidatorModel;
}
