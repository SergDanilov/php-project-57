<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

// Главная страница
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/locale/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'ru'])) {
        abort(400);
    }

    Cookie::queue('locale', $locale, 60 * 24 * 365); // 1 год
    session()->put('locale', $locale);

    return redirect()->back();
})->name('setlocale');



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

    // Task CRUD (кроме index и show)
    Route::resource('tasks', TaskController::class)
        ->except(['index', 'show'])
        ->names([
            'create' => 'tasks.create',
            'store' => 'tasks.store',
            'edit' => 'tasks.edit',
            'update' => 'tasks.update',
            'destroy' => 'tasks.destroy',
        ]);

    Route::resource('labels', LabelController::class)
        ->except(['index', 'show'])
        ->names([
            'create' => 'labels.create',
            'store' => 'labels.store',
            'edit' => 'labels.edit',
            'update' => 'labels.update',
            'destroy' => 'labels.destroy',
        ]);

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Общие маршруты (доступны всем)
Route::controller(StatusController::class)->group(function () {
    Route::get('/task_statuses', 'index')->name('task_statuses.index');
    Route::get('/task_statuses/{status}', 'show')->name('task_statuses.show');
});
Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index')->name('tasks.index');
    Route::get('/tasks/{task}', 'show')->name('tasks.show');
});
Route::controller(LabelController::class)->group(function () {
    Route::get('/labels', 'index')->name('labels.index');
    Route::get('/labels/{label}', 'show')->name('labels.show');
});

require __DIR__ . '/auth.php';
