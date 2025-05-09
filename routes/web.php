<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Общие маршруты (доступны всем)
Route::controller(StatusController::class)->group(function () {
    Route::get('/task_statuses', 'index')->name('task_statuses.index');
    Route::get('/task_statuses/{status}', 'show')->name('task_statuses.show');
});

// Защищенные маршруты (только для авторизованных)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Status CRUD (кроме index и show)
    Route::resource('task_statuses', StatusController::class)
        ->except(['index', 'show'])
        ->names([
            'create' => 'task_statuses.create',
            'store' => 'task_statuses.store',
            'edit' => 'task_statuses.edit',
            'update' => 'task_statuses.update',
            'destroy' => 'task_statuses.destroy',
        ]);

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';