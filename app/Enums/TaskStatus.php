<?php

namespace App\Enums;

use App\Support\ExtendsEnum;

enum TaskStatus: int
{
    use ExtendsEnum;

    case Pending = 1;
    case Failed = 2;
    case MaskStarting = 3;
    case MaskCompleted = 4;
    case CleanerStarting = 5;
    case CleanerCompleted = 6;

    public static function mapped(): array
    {
        return [
            'pending' => self::Pending,
            'failed' => self::Failed,
            'mask_starting' => self::MaskStarting,
            'mask_completed' => self::MaskCompleted,
            'cleaner_starting' => self::CleanerStarting,
            'cleaner_completed' => self::CleanerCompleted,
        ];
    }

    public function hasMask(): bool
    {
        return in_array($this->value, [
            self::MaskCompleted->value,
            self::CleanerStarting->value,
            self::CleanerCompleted->value,
        ]);
    }

    public function hasResult(): bool
    {
        return $this->value === self::CleanerCompleted->value;
    }
}
