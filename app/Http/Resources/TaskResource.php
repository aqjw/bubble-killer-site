<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'files' => $this->files,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'status' => $this->status->getName(),
            'cleaning_model' => $this->cleaning_model,
            'original_filename' => $this->original_filename,
            'mask_time_seconds' => $this->maskTimeSeconds,
            'clean_time_seconds' => $this->cleanTimeSeconds,
            'created_at' => $this->created_at,
            'subtasks' => $this->subtasks->isEmpty() ? [] : TaskResource::collection($this->subtasks),
        ];
    }
}
