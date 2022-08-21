<?php

namespace Paliari\Doctrine;

use Exception;
use Paliari\Doctrine\Validators\ModelCustomValidator;

trait TraitValidatorModel
{
    protected string $record_state = '';

    protected static array $before_validation = [];
    protected static array $after_validation = [];
    protected static array $before_validation_on_create = [];
    protected static array $after_validation_on_create = [];
    protected static array $before_validation_on_update = [];
    protected static array $after_validation_on_update = [];
    protected static array $before_validation_on_remove = [];
    protected static array $after_validation_on_remove = [];

    protected static array $validates_presence_of = [];
    protected static array $validates_size_of = [];
    protected static array $validates_length_of = [];
    protected static array $validates_inclusion_of = [];
    protected static array $validates_exclusion_of = [];
    protected static array $validates_format_of = [];
    protected static array $validates_numericality_of = [];
    protected static array $validates_uniqueness_of = [];
    protected static array $validates_custom = [];

    /**
     * @return callable[]
     */
    public static function getValidates(string $name): array
    {
        return static::${$name} ?? [];
    }

    public ?ValidatorErrors $errors = null;

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

    /**
     * @throws ModelException
     */
    public function validate(): void
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

    public function setRecordState(string $recordState): void
    {
        $this->record_state = $recordState;
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
