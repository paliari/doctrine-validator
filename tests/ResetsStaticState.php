<?php

use Paliari\Doctrine\Validator;
use Paliari\Doctrine\Validators\ModelCustomValidator;

trait ResetsStaticState
{

    private array $static_backups = [];

    protected function backupStatic(string $class, string $property): void
    {
        $reflection             = new ReflectionProperty($class, $property);
        $this->static_backups[] = [$reflection, $reflection->getValue()];
    }

    protected function restoreStatics(): void
    {
        foreach (array_reverse($this->static_backups) as [$reflection, $value]) {
            $reflection->setValue(null, $value);
        }
        $this->static_backups = [];
    }

    protected function resetValidatorCaches(): void
    {
        (new ReflectionProperty(Validator::class, '_validates'))->setValue(null, []);
        (new ReflectionProperty(ModelCustomValidator::class, '_validators'))->setValue(ModelCustomValidator::i(), []);
    }

}
