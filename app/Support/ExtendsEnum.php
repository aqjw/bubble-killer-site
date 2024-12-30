<?php

namespace App\Support;

use UnitEnum;

trait ExtendsEnum
{
    public static function mapped(): array
    {
        return [];
    }

    public static function fromName(?string $name, $default = null): ?self
    {
        return self::mapped()[$name] ?? $default;
    }

    public function is(UnitEnum|int|string|null $enum): bool
    {
        return $this->value === ($enum instanceof UnitEnum ? $enum->value : $enum);
    }

    public function in(array $cases): bool
    {
        foreach ($cases as $case) {
            if ($this->is($case)) {
                return true;
            }
        }
        return false;
    }

    public function getName(): ?string
    {
        foreach (self::mapped() as $name => $enum) {
            if ($enum === $this) {
                return $name;
            }
        }

        return null;
    }

    public static function getCases(): array
    {
        return array_map(
            fn ($item) => [
                'id' => $item->value,
                'name' => $item->getName(),
            ],
            self::cases()
        );
    }
}
