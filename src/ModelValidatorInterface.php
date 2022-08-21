<?php

namespace Paliari\Doctrine;

use Doctrine\ORM\EntityManager;

interface ModelValidatorInterface
{
    public function validate(): void;

    public function isValid(bool $throw = false): bool;

    public function setRecordState(string $recordState): void;

    public function isNewRecord(): bool;

    public function isRemoveRecord(): bool;

    public function isUpdateRecord(): bool;

    public function recordState(): string;

    public static function className(): string;

    public static function humAttribute(string $name): ?string;

    public static function addCustomValidator(callable $callable): void;

    public function validateModelCustom(): void;

    /**
     * @return callable[]
     */
    public static function getValidates(string $name): array;

    /**
     * @return EntityManager
     */
    public static function getEM();
}
