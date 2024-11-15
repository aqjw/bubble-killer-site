<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Services\SegmentationService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $id
 * @property TaskStatus $status
 * @property array $execution_time
 * @property array $files
 * @property string $type
 * @property string $cleaning_model
 */
class Task extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'user_id',
        'type',
        'status',
        'cleaning_model',
        'original_filename',
        'segmentation_id',
        'execution_time',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'execution_time' => 'array',
    ];

    protected static function booted()
    {
        static::updating(function (self $task) {
            if ($task->isDirty('status')) {
                $execution_time = $task->execution_time ?? [];
                $execution_time[$task->status->getName()] = now();
                $task->attributes['execution_time'] = json_encode($execution_time);
            }
        });
    }


    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function files(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'original' => Storage::url("uploads/{$this->id}/original.png"),
                'mask' => $this->status->hasMask() ? Storage::url("uploads/{$this->id}/mask.png") : null,
                'result' => $this->status->hasResult() ? Storage::url("uploads/{$this->id}/result.png") : null,
            ]
        );
    }

    public function multiple(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'multiple'
        );
    }

    public function maskTimeSeconds(): Attribute
    {
        return Attribute::make(
            get: function () {
                $start = $this->execution_time['mask_starting'] ?? null;
                $end = $this->execution_time['mask_completed'] ?? null;

                return $start && $end
                    ? (strtotime($end) - strtotime($start))
                    : null;
            }
        );
    }

    public function cleanTimeSeconds(): Attribute
    {
        return Attribute::make(
            get: function () {
                $start = $this->execution_time['cleaner_starting'] ?? null;
                $end = $this->execution_time['cleaner_completed'] ?? null;

                return $start && $end
                    ? (strtotime($end) - strtotime($start))
                    : null;
            }
        );
    }
}
