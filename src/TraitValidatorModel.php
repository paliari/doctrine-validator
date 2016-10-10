<?php
namespace Paliari\Doctrine;

use Doctrine\ORM\EntityManager;

trait TraitValidatorModel
{

    protected $record_state = '';

    protected static $before_validation           = [];
    protected static $after_validation            = [];
    protected static $before_validation_on_create = [];
    protected static $after_validation_on_create  = [];
    protected static $before_validation_on_update = [];
    protected static $after_validation_on_update  = [];

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
     * @return array
     */
    public static function getValidatesPresenceOf()
    {
        return static::$validates_presence_of;
    }

    /**
     * @return array
     */
    public static function getValidatesSizeOf()
    {
        return static::$validates_size_of;
    }

    /**
     * @return array
     */
    public static function getValidatesLengthOf()
    {
        return static::$validates_length_of;
    }

    /**
     * @return array
     */
    public static function getValidatesInclusionOf()
    {
        return static::$validates_inclusion_of;
    }

    /**
     * @return array
     */
    public static function getValidatesExclusionOf()
    {
        return static::$validates_exclusion_of;
    }

    /**
     * @return array
     */
    public static function getValidatesFormatOf()
    {
        return static::$validates_format_of;
    }

    /**
     * @return array
     */
    public static function getValidatesNumericalityOf()
    {
        return static::$validates_numericality_of;
    }

    /**
     * @return array
     */
    public static function getValidatesUniquenessOf()
    {
        return static::$validates_uniqueness_of;
    }

    /**
     * @return array
     */
    public static function getValidatesCustom()
    {
        return static::$validates_custom;
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
        $this->_validate();
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
        $this->_validate();
    }

    /**
     * @ORM\PreUpdate
     */
    public function _onDoToValidatePreUpdate()
    {
        $this->record_state = Validator::UPDATE;
        $this->_validate();
    }

    protected function _doValidation($action)
    {
        $action .= '_validation';
        $callbacks = @$this->$action ?: [];
        $action .= '_on_' . $this->recordState();
        $callbacks = array_merge($callbacks, @$this->$action ?: []);
        foreach ($callbacks as $callback) {
            $this->$callback();
        }
    }

    /**
     * @return bool
     */
    protected function _validate()
    {
        $this->_defaultValidates();
        $this->_doValidation('before');
        $validator = new Validator($this);
        $validator->validate();
        $this->_doValidation('after');
    }

    protected function _defaultValidates()
    {
        if (isset(MappingsValidates::$cache[static::className()])) {
            return;
        }
        $length       = MappingsValidates::getDefaults(static::className(), 'validates_length_of');
        $numericality = MappingsValidates::getDefaults(static::className(), 'validates_numericality_of');
        $inclusion    = MappingsValidates::getDefaults(static::className(), 'validates_inclusion_of');

        static::$validates_length_of       = array_merge_recursive($length, static::$validates_length_of);
        static::$validates_numericality_of = array_merge_recursive($numericality, static::$validates_numericality_of);
        static::$validates_inclusion_of    = array_merge_recursive($inclusion, static::$validates_inclusion_of);
    }

    public function isValid()
    {
        $this->_validate();

        return $this->errors->isValid();
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

    /**
     * @return EntityManager
     * @throws \Exception
     */
    public static function getEm()
    {
        throw new \Exception('Method "getEm" not be implemented!');
    }

    public static function className()
    {
        return get_called_class();
    }

    public static function humAttribute($name)
    {
        return $name;
    }

}
