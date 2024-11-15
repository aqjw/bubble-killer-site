<?php

namespace App\Services;

use App\Jobs\ExtractSubtasksJob;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SegmentationService
{
    public function send(Task $task)
    {
        if (blank($task->files['original'])) {
            return;
        }

        $domain = env('AI_MODEL_ENDPOINT');
        $response = Http::post("{$domain}/process", [
            'task_id' => $task->id,
            'image_url' => $task->files['original'], // s3/spaces
            'cleaning_model' => $task->cleaning_model,
            'webhook_url' => route('api.webhook.processing', $task->id),
            // 'webhook_url' => 'http://127.0.0.1:8000/api/webhook/processing/' . $task->id,
        ]);

        if ($response->ok()) {
            // TODO
        }
    }
}
