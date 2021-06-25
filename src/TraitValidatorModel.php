<?php

namespace Paliari\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Paliari\Doctrine\Validators\ModelCustomValidator;

trait TraitValidatorModel
{
    protected $record_state = '';

    protected static $before_validation = [];
    protected static $after_validation = [];
    protected static $before_validation_on_create = [];
    protected static $after_validation_on_create = [];
    protected static $before_validation_on_update = [];
    protected static $after_validation_on_update = [];
    protected static $before_validation_on_remove = [];
    protected static $after_validation_on_remove = [];

    protected static $validates_presence_of = [];
    protected static $validates_size_of = [];
    protected static $validates_length_of = [];
    protected static $validates_inclusion_of = [];
    protected static $validates_exclusion_of = [];
    protected static $validates_format_of = [];
    protected static $validates_numericality_of = [];
    /**
     * @deprecated Usar outra estratégia para esta validação
     * Estamos removendo a dependencia do EM no model.
     */
    protected static $validates_uniqueness_of = [];
    protected static $validates_custom = [];

    /**
     * @return callable[]
     */
    public static function getValidates(string $name): array
    {
        return static::${$name} ?? [];
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
        $action .= '_validation';
        $callbacks = static::${$action} ?: [];
        $action .= '_on_' . $this->recordState();
        $callbacks = array_merge($callbacks, static::${$action} ?: []);
        foreach ($callbacks as $callback) {
            $this->$callback();
        }
    }

    protected function validate(): void
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

    public function isValid(bool $throw = false): bool
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

    public function isNewRecord(): bool
    {
        return Validator::CREATE == $this->record_state;
    }

    public function isRemoveRecord(): bool
    {
        return Validator::REMOVE == $this->record_state;
    }

    public function isUpdateRecord(): bool
    {
        return Validator::UPDATE == $this->record_state;
    }

    public function recordState(): string
    {
        return $this->record_state ?: Validator::CREATE;
    }

    public static function className(): string
    {
        return static::class;
    }

    public static function addCustomValidator($callable): void
    {
        ModelCustomValidator::i()->add(static::className(), $callable);
        static::$validates_custom['validateModelCustom'] = [];
    }

    public function validateModelCustom(): void
    {
        ModelCustomValidator::i()->run(static::className(), $this);
    }
}
