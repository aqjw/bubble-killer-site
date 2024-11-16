<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    public function processing(Task $task, Request $request)
    {
        // Получаем параметры из запроса
        $status = $request->input('status');

        // Обновление статуса задачи
        rescue(fn () => $task->update(['status' => TaskStatus::fromName($status)]));

        if ($request->hasFile('mask')) {
            $request->file('mask')->storeAs("uploads/{$task->id}", 'mask.png', 'spaces');
            Log::info("Task {$task->id} mask file received");
        }

        if ($request->hasFile('result')) {
            $request->file('result')->storeAs("uploads/{$task->id}", 'result.png', 'spaces');
            Log::info("Task {$task->id} result file received");
        }

        Log::info("Task {$task->id} status updated to {$status}");

        return response()->json('ok');
    }
}
