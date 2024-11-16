<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UploadController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');
Route::post('upload', UploadController::class)->name('upload');
Route::get('task/{task}', TaskController::class)->name('task');


Route::middleware('auth')->group(function () {
    Route::get('history', [TaskController::class, 'history'])->name('history');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/upi.php';
require __DIR__ . '/auth.php';
