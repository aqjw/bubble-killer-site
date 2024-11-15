<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Jobs\ExtractSubtasksJob;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SegmentationService
{
    public function send(Task $task)
    {
        // Validate if original file exists
        if (blank($task->files['original'])) {
            Log::warning("Task {$task->id} has no original file.");
            return;
        }

        $domain = env('AI_MODEL_ENDPOINT');

        try {
            // Send POST request to AI model
            $response = Http::post("{$domain}/process", [
                'task_id' => $task->id,
                'image_url' => $task->files['original'], // S3/Spaces URL
                'cleaning_model' => $task->cleaning_model,
                // 'webhook_url' => route('api.webhook.processing', $task->id),
                'webhook_url' => 'http://127.0.0.1:8000/api/webhook/processing/' . $task->id,
            ]);

            if ($response->ok()) {
                Log::info("Task {$task->id} sent to AI model successfully.");
            } else {
                Log::error("Failed to send Task {$task->id} to AI model.", ['response' => $response->body()]);
                $task->update(['status' => TaskStatus::Failed]);
            }
        } catch (\Exception $e) {
            Log::error("Error while sending Task {$task->id} to AI model.", ['exception' => $e->getMessage()]);
            $task->update(['status' => TaskStatus::Failed]);
        }
    }
}
