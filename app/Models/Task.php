<?php

namespace App\Models;

use App\Services\SegmentationService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;


    protected $fillable = [
        'parent_id',
        'type',
        'status', // pending mask_starting, mask_completed, cleaner_starting, cleaner_completed
        'cleaning_model',
        'original_filename',
        'segmentation_id',
        'time',
    ];

    protected $casts = [
        'time' => 'array',
    ];

    protected static function booted()
    {
        static::updating(function ($task) {
            if ($task->isDirty('status')) {
                $task->time ??= [];

                $startKey = "{$task->status}_starting";
                $durationKey = "{$task->status}_duration";

                if (isset($task->time[$startKey])) {
                    $task->time[$durationKey] = now()->diffInSeconds($task->time[$startKey]);
                }

                if (in_array($task->status, ['mask_starting', 'cleaner_starting'])) {
                    $task->time[$task->status] = now();
                }
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

    public function getFilesAttribute()
    {
        return [
            'original' => Storage::url("uploads/{$this->id}/original.png"),
            'mask' => Storage::url("uploads/{$this->id}/mask.png"),
            'result' => Storage::url("uploads/{$this->id}/result.png"),
        ];
    }

    public function getMultipleAttribute()
    {
        return $this->type === 'multiple';
    }
}
