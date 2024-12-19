<?php

namespace App\Enums;

use App\Support\ExtendsEnum;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MangaChapterStatus: int implements HasLabel, HasColor
{
    use ExtendsEnum;

    // Step 1: Waiting in queue
    case Pending = 1;

    // Step 2: Parsing images
    case ImageParsing = 2;

    // Step 3: Splitting images into obvious parts
    case ImageSplitting = 3;

    // Step 4: Filtering out unnecessary images
    case ImageFiltering = 4;

    // Step 5: Creating masks (AI)
    case MaskCreation = 5;

    case QualityImprovement = 6;

    // Step 6: Checking and correcting masks (human)
    case MaskVerification = 7;

    // Step 7: Removing bubbles (AI)
    case BubbleRemoval = 8;

    // Step 8: Checking and correcting image clearing (human)
    case ClearVerification = 9;

    // Step 9: Creating masks for cropped frames (AI)
    case FrameMaskCreation = 10; // TODO: остановился на этом шаге.

    // Step 10: Verifying frame masks
    case FrameMaskVerification = 11;

    // Step 11: Cropping frame images
    case FrameCropping = 12;

    // Step 12: Processing completed
    case Finalized = 13;

    /**
     * Maps statuses to human-readable keys.
     */
    public static function mapped(): array
    {
        return [
            'pending' => self::Pending,
            'image_parsing' => self::ImageParsing,
            'image_splitting' => self::ImageSplitting,
            'image_filtering' => self::ImageFiltering,
            'mask_creation' => self::MaskCreation,
            'quality_improvement' => self::QualityImprovement,
            'mask_verification' => self::MaskVerification,
            'bubble_removal' => self::BubbleRemoval,
            'clear_verification' => self::ClearVerification,
            'frame_mask_creation' => self::FrameMaskCreation,
            'frame_mask_verification' => self::FrameMaskVerification,
            'frame_cropping' => self::FrameCropping,
            'finalized' => self::Finalized,
        ];
    }

    public function getLabel(): string
    {
        return match ($this->value) {
            self::Pending->value => 'Waiting in Queue',
            self::ImageParsing->value => 'Parsing Images',
            self::ImageSplitting->value => 'Splitting Images',
            self::ImageFiltering->value => 'Filtering Unnecessary Images',
            self::QualityImprovement->value => 'Quality Improvement',
            self::MaskCreation->value => 'Creating Masks',
            self::MaskVerification->value => 'Mask Verification',
            self::BubbleRemoval->value => 'Removing Bubbles',
            self::ClearVerification->value => 'Clear Verification',
            self::FrameMaskCreation->value => 'Creating Frame Masks',
            self::FrameMaskVerification->value => 'Verifying Frame Masks',
            self::FrameCropping->value => 'Reviewing Cropped Images',
            self::Finalized->value => 'Finalized',
        };
    }

    public function getColor(): array
    {
        return match ($this->value) {
            self::Pending->value => Color::Gray,
            self::ImageParsing->value => Color::Blue,
            self::ImageSplitting->value => Color::Cyan,
            self::ImageFiltering->value => Color::Slate,
            self::QualityImprovement->value => Color::Indigo,
            self::MaskCreation->value => Color::Indigo,
            self::MaskVerification->value => Color::Yellow,
            self::BubbleRemoval->value => Color::Red,
            self::ClearVerification->value => Color::Orange,
            self::FrameMaskCreation->value => Color::Violet,
            self::FrameMaskVerification->value => Color::Fuchsia,
            self::FrameCropping->value => Color::Emerald,
            self::Finalized->value => Color::Green,
        };
    }
}
