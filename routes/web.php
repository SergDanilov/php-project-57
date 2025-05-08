<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Доступно всем
Route::get('/task_statuses', [StatusController::class, 'index'])
    ->name('task_statuses.index');

// Только для авторизованных
Route::middleware('auth')->group(function () {
    Route::resource('task_statuses', StatusController::class)
        ->except(['index', 'show'])
        ->names([
            'create' => 'task_statuses.create',
            'store' => 'task_statuses.store',
            'edit' => 'task_statuses.edit',
            'update' => 'task_statuses.update',
            'destroy' => 'task_statuses.destroy',
        ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
