<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TaskController extends Controller
{

    public function __invoke(Task $task)
    {
        return Inertia::render('Task', [
            'task' => new TaskResource($task),
        ]);
    }

    public function history()
    {
        return Inertia::render('History');
    }
}
