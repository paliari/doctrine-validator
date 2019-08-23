<?php

namespace Paliari\Doctrine;

use Exception;

trait TraitValidatorModel
{

    protected $record_state = '';

    protected static $before_validation           = [];
    protected static $after_validation            = [];
    protected static $before_validation_on_create = [];
    protected static $after_validation_on_create  = [];
    protected static $before_validation_on_update = [];
    protected static $after_validation_on_update  = [];
    protected static $before_validation_on_remove = [];
    protected static $after_validation_on_remove  = [];

    protected static $validates_presence_of     = [];
    protected static $validates_size_of         = [];
    protected static $validates_length_of       = [];
    protected static $validates_inclusion_of    = [];
    protected static $validates_exclusion_of    = [];
    protected static $validates_format_of       = [];
    protected static $validates_numericality_of = [];
    protected static $validates_uniqueness_of   = [];
    protected static $validates_custom          = [];

    /**
     * @param string $name
     *
     * @return array
     */
    public static function getValidates($name)
    {
        return @static::${$name} ?: [];
    }

    /**
     * @var ValidatorErrors
     */
    public $errors;

    /**
     * @ORM\PostLoad
     */
    public function _onDoToValidatePostLoad()
    {
        $this->record_state = Validator::UPDATE;
    }

    /**
     * @ORM\PrePersist
     */
    public function _onDoToValidatePrePersist()
    {
        $this->record_state = Validator::CREATE;
        $this->validate();
    }

    /**
     * @ORM\PostPersist
     */
    public function _onDoToValidatePostPersist()
    {
        $this->record_state = Validator::UPDATE;
    }

    /**
     * @ORM\PreRemove
     */
    public function _onDoToValidatePreRemove()
    {
        $this->record_state = Validator::REMOVE;
        $this->validate();
    }

    /**
     * @ORM\PreUpdate
     */
    public function _onDoToValidatePreUpdate()
    {
        $this->record_state = Validator::UPDATE;
        $this->validate();
    }

    protected function actionValidation($action)
    {
        $action    .= '_validation';
        $callbacks = static::${$action} ?: [];
        $action    .= '_on_' . $this->recordState();
        $callbacks = array_merge($callbacks, static::${$action} ?: []);
        foreach ($callbacks as $callback) {
            $this->$callback();
        }
    }

    protected function validate()
    {
        $validated = $this->errors instanceof ValidatorErrors;
        if (!$validated) {
            $this->actionValidation('before');
            $validator = new Validator($this);
            $validator->validate();
        }
        if (!$this->errors->isValid()) {
            throw new ModelException($this->errors);
        }
        if (!$validated) {
            $this->actionValidation('after');
        }
    }

    public function isValid($throw = false)
    {
        try {
            $this->validate();

            return true;
        } catch (ModelException $e) {
            if ($throw) {
                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isNewRecord()
    {
        return Validator::CREATE == $this->record_state;
    }

    /**
     * @return string
     */
    public function recordState()
    {
        return $this->record_state ?: Validator::CREATE;
    }

    public static function className()
    {
        return get_called_class();
    }

}
