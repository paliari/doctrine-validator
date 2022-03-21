<?php

namespace Paliari\Doctrine;

use Exception;

class ModelException extends Exception
{
    protected $validator_errors;

    /**
     * ValidatorErrorsException constructor.
     *
     * @param string|ValidatorErrors $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $this->validator_errors = $message;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ValidatorErrors
     */
    public function getValidatorErrors()
    {
        return $this->validator_errors;
    }
}
