<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WebhookController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');
Route::post('upload', UploadController::class)->name('upload');

Route::prefix('tasks')->as('tasks.')->group(function () {
    Route::get('', [TaskController::class, 'index'])->name('index')->middleware('auth');
    Route::get('{task}', [TaskController::class, 'show'])->name('show');
});

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('api/webhook/processing', [WebhookController::class, 'processing'])->name('api.webhook.processing');

require __DIR__ . '/upi.php';
require __DIR__ . '/auth.php';
