<?php

namespace App\Http\Controllers\UPI;

use App\Enums\BookmarkType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UPI\UpdateProfileInformationRequest;
use App\Http\Resources\ActivityHistoryResource;
use App\Http\Resources\BookmarkCardResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ActivityHistoryService;
use App\Services\BookmarkService;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function __invoke(TaskService $taskService): JsonResponse
    {
        $result = $taskService->get(auth()->user());

        return response()->json([
            'items' => TaskResource::collection($result->items()),
            'total' => $result->total(),
            'has_more' => $result->hasMorePages(),
        ]);
    }
}
