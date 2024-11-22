<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UploadController extends Controller
{
    public function __invoke(Request $request, TaskService $taskService): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'max:10'],
            'files.*' => ['file', 'mimes:jpeg,png,jpg,zip', 'max:10240'],
            'model' => ['required', 'string', 'in:' . implode(',', config('app.cleaner_models'))],
        ]);

        $model = $request->input('model');
        $files = $request->file('files');
        $user = $request->user();

        if (! $user) {
            if (count($files) > 1) {
                throw ValidationException::withMessages([
                    'files' => ['Only authorized users can upload Multiple files.'],
                ]);
            }

            foreach ($files as $file) {
                if ($file->getClientOriginalExtension() === 'zip') {
                    throw ValidationException::withMessages([
                        'files' => ['Only authorized users can upload ZIP files.'],
                    ]);
                }
            }
        }

        $tasks = [];

        foreach ($files as $file) {
            $tasks[] = $taskService->create($file, $model);
        }

        if (! $user) {
            return to_route('tasks.show', $tasks[0]);
        }

        return to_route('tasks.index');
    }
}
