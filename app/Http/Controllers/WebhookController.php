<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    public function processing(Request $request)
    {
        // Получаем параметры из запроса
        $taskId = $request->input('task_id');
        $status = $request->input('status');

        // Log::error('hasfile - ' . $request->hasFile('file'));

        // Проверка наличия задачи
        $task = Task::findOrFail($taskId);

        // Обновление статуса задачи
        $task->status = $status;
        $task->save();

        // Если передан файл, сохраняем его
        if ($request->hasFile('result')) {
            $request->file('result')->storeAs("uploads/{$task->id}", 'result.png', 'spaces');
        }

        if ($request->hasFile('mask')) {
            $request->file('mask')->storeAs("uploads/{$task->id}", 'mask.png', 'spaces');
        }

        Log::info("Task {$taskId} status updated to {$status}");

        return response()->json('ok');
    }
}
