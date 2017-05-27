<?php
namespace Paliari\Doctrine;

use Paliari\Doctrine\Validators\FilterVarValidator,
    Doctrine\Common\Inflector\Inflector;

class Validator
{

    const SAVE   = 'save';
    const CREATE = 'create';
    const UPDATE = 'update';
    const REMOVE = 'remove';

    protected static $_validates = [];

    protected static $_validations_default_names = [
        'validates_length_of',
        'validates_numericality_of',
        'validates_inclusion_of',
    ];

    protected static $_validations_names = [
        'validates_presence_of',
        'validates_size_of',
        'validates_length_of',
        'validates_inclusion_of',
        'validates_exclusion_of',
        'validates_format_of',
        'validates_numericality_of',
        'validates_uniqueness_of',
        'validates_custom',
    ];

    /**
     * @var InterfaceModel
     */
    protected $model;

    /**
     * Validator constructor.
     *
     * @param InterfaceModel $model
     */
    public function __construct($model)
    {
        $this->model                     = $model;
        $this->model->errors             = new ValidatorErrors();
        $this->model->errors->model_name = $model->className();
        $this->init();
    }

    protected function init()
    {
        $model_name = $this->model->className();
        if (!isset(static::$_validates[$model_name])) {
            static::initValidates($model_name);
        }
    }

    public static function initValidates($model_name)
    {
        static::defaultValidates($model_name);
        foreach (static::$_validations_names as $validation) {
            static::setValidates($model_name, $validation, $model_name::getValidates($validation));
        }
    }

    protected static function defaultValidates($model_name)
    {
        foreach (static::$_validations_default_names as $name) {
            static::setValidates($model_name, $name, MappingsValidates::getDefaults($model_name, $name));
        }
    }

    protected static function setValidates($model, $validation_name, $validation)
    {
        if (isset(static::$_validates[$model][$validation_name])) {
            $validation = static::merge(static::$_validates[$model][$validation_name], $validation);
        }
        static::$_validates[$model][$validation_name] = $validation;
    }

    protected static function getValidates($model, $validation_name)
    {
        return @static::$_validates[$model][$validation_name] ?: [];
    }

    public function validate()
    {
        foreach (static::$_validations_names as $validation) {
            $validation_method = Inflector::camelize(str_replace('validates_', '', $validation));
            $this->validates($validation_method, static::getValidates($this->model->className(), $validation));
        }
    }

    protected function validates($validation_method, $attributes = [])
    {
        foreach ($attributes as $field => $options) {
            if (is_string($options)) {
                $field   = $options;
                $options = [];
            }
            if (!$this->skipValidation($field, $options)) {
                $this->$validation_method($field, $options);
            }
        }
    }

    protected function skipValidation($field, $options)
    {
        if (static::REMOVE == $this->model->recordState() && static::REMOVE != @$options['on']) {
            return true;
        }
        if (isset($options['if']) && !$this->model->$options['if']) {
            return true;
        }
        if (isset($options['unless']) && $this->model->$options['unless']) {
            return true;
        }
        if (@$options['on'] && static::SAVE != $options['on']) {
            return $this->model->recordState() != $options['on'];
        }
        if (@$options['allow_nil'] && null === $this->model->$field) {
            return true;
        }
        if (@$options['allow_blank'] && $this->isBlank($this->model->$field)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function presenceOf($field, $options)
    {
        if ($this->isBlank($this->model->$field)) {
            $this->add($field, $this->getMessage($options, 'blank'));
        }
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function sizeOf($field, $options)
    {
        $this->lengthOf($field, $options);
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function lengthOf($field, $options)
    {
        $size    = mb_strlen($this->model->$field, 'UTF-8');
        $minimum = @$options['minimum'];
        $maximum = @$options['maximum'];
        if ($in = @$options['within'] ?: @$options['in']) {
            list($minimum, $maximum) = $in;
        }
        if ($minimum && $size < $minimum) {
            $this->add($field, $this->getMessage($options, 'too_short'), $minimum);
        }
        if ($maximum && $size > $maximum) {
            $this->add($field, $this->getMessage($options, 'too_long'), $maximum);
        }
        if (isset($options['is']) && $size != $options['is']) {
            $this->add($field, $this->getMessage($options, 'wrong_length'), $options['is']);
        }
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function inclusionOf($field, $options)
    {
        $in = @$options['in'] ?: @$options['within'] ?: [];
        if (!in_array($this->model->$field, $in, true)) {
            $this->add($field, $this->getMessage($options, 'inclusion'));
        }
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function exclusionOf($field, $options)
    {
        $in = @$options['in'] ?: @$options['within'] ?: [];
        if (in_array($this->model->$field, $in, true)) {
            $this->add($field, $this->getMessage($options, 'exclusion'));
        }
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function formatOf($field, $options)
    {
        if ($with = @$options['with']) {
            if (!$this->checkFilterVar($this->model->$field, $with)) {
                $this->add($field, $this->getMessage($options, 'invalid'));
            }
        }
        if ($without = @$options['without']) {
            if ($this->checkFilterVar($this->model->$field, $without)) {
                $this->add($field, $this->getMessage($options, 'invalid'));
            }
        }
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function numericalityOf($field, $options)
    {
        $value = $this->model->$field;
        if (@$options['only_integer'] && !$this->isInteger($value)) {
            $this->add($field, $this->getMessage($options, 'not_a_integer'));
        }
        if (!$this->isNumber($value)) {
            $this->add($field, $this->getMessage($options, 'not_a_number'));
        }
        foreach ($options as $comparator => $than) {
            if (!$this->comparatorThan($comparator, $value, $than)) {
                $this->add($field, $this->getMessage($options, $comparator), $than);
            }
        }
    }

    /**
     * @param string $field
     * @param array  $options
     */
    public function uniquenessOf($field, $options)
    {
        $criteria = [$field => $this->model->$field];
        foreach ((array)@$options['scope'] as $scope) {
            $criteria[$scope] = $this->model->$scope;
        }
        $olds = $this->model->getEm()->getRepository($this->model->className())->findBy($criteria, null, 1);
        $old  = @$olds[0];
        if ($old && $old->id != $this->model->id) {
            $this->add($field, $this->getMessage($options, 'unique'));
        }
    }

    /**
     * @param string $method
     * @param array  $options
     */
    public function custom($method, $options = null)
    {
        $this->model->$method();
    }

    /**
     * @param string $comparator
     * @param mixed  $value
     * @param float  $than
     *
     * @return bool
     */
    protected function comparatorThan($comparator, $value, $than)
    {
        switch ($comparator) {
            case 'greater_than' :
                return $value > $than;
            case 'greater_than_or_equal_to' :
                return $value >= $than;
            case 'less_than' :
                return $value < $than;
            case 'less_than_or_equal_to' :
                return $value <= $than;
            case 'equal_to' :
                return $value == $than;
            case 'other_than' :
                return $value != $than;
        }

        return true;
    }

    /**
     * @param mixed  $value
     * @param string $filter
     *
     * @return bool
     */
    protected function checkFilterVar($value, $filter)
    {
        return FilterVarValidator::instance()->check($value, $filter);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isBlank($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        if ('0' == $value) {
            return false;
        }

        return empty($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isInteger($value)
    {
        return $this->checkFilterVar($value, 'integer');
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isNumber($value)
    {
        return $this->checkFilterVar($value, 'float');
    }

    /**
     * @param array  $options
     * @param string $key
     *
     * @return string
     */
    protected function getMessage($options, $key = 'invalid')
    {
        return @$options[$key] ?: @$options['message'] ?: $this->model->errors->getDefaultMessage($key);
    }

    /**
     * @param string $message
     * @param mixed  $replace
     * @param string $search
     *
     * @return string
     */
    protected function replaceMessage($message, $replace, $search = '%{count}')
    {
        return str_replace($search, $replace, $message);
    }

    /**
     * @param string $field
     * @param string $message
     * @param mixed  $replace
     * @param string $search
     */
    protected function add($field, $message, $replace = null, $search = '%{count}')
    {
        if ($replace) {
            $message = $this->replaceMessage($message, $replace, $search);
        }
        $this->model->errors->add($field, $message);
    }

    protected static function merge($a1, $a2)
    {
        $a1 = (array)$a1;
        $a2 = (array)$a2;
        foreach ($a1 as $k => $v) {
            if (is_array($v) && @$a2[$k]) {
                $a1[$k] = $a2[$k] = static::merge($v, $a2[$k]);
            }
        }

        return array_merge($a1, $a2);
    }

}
