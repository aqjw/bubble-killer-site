<?php

use App\Http\Controllers\UPI\BookmarkController;
use App\Http\Controllers\UPI\CommentController;
use App\Http\Controllers\UPI\NotificationController;
use App\Http\Controllers\UPI\ProfileController;
use App\Http\Controllers\UPI\SearchController;
use App\Http\Controllers\UPI\SettingsController;
use App\Http\Controllers\UPI\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('upi')->as('upi.')->group(function () {

    Route::middleware('auth')->group(function () {
        Route::get('tasks', TaskController::class)->name('tasks');

        Route::prefix('profile')->as('profile.')->group(function () {
            Route::post('information', [ProfileController::class, 'updateInformation'])->name('update_information');
            Route::delete('destroy', [ProfileController::class, 'destroy'])->name('destroy');
        });
    });
});
