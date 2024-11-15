<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::post('api/webhook/processing/{task}', [WebhookController::class, 'processing'])
    ->name('api.webhook.processing');

Volt::route('', 'pages.welcome')
    ->name('welcome');

Route::get('task/{task}', [TaskController::class, 'show'])
    ->name('task');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
