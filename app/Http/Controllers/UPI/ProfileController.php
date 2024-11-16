<?php

namespace App\Http\Controllers\UPI;

use App\Enums\BookmarkType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UPI\UpdateProfileInformationRequest;
use App\Http\Resources\ActivityHistoryResource;
use App\Http\Resources\BookmarkCardResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ActivityHistoryService;
use App\Services\BookmarkService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function updateInformation(Request $request, UserService $userService)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($request->user()->id),
            ],
        ]);

        $userService->updateInfo(
            user: $request->user(),
            data: $data
        );

        return back();
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // TODO: delete profile
        throw ValidationException::withMessages([
            'message' => 'error',
        ]);
    }
}
