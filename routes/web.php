<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

// Locale
Route::get('/locale/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'ru'], true)) {
        abort(400);
    }

    Cookie::queue('locale', $locale, 60 * 24 * 365);
    session()->put('locale', $locale);

    return redirect()->back();
})->name('setlocale');

// Main page - open for all users
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    // Профиль (оставляем middleware, так как это стандартный функционал Laravel)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Tasks routes
Route::resource('tasks', TaskController::class)
    ->except('index', 'show')
    ->middleware(['auth', 'verified']);

Route::controller(TaskController::class)->group(function () {
    Route::get('/tasks', 'index')->name('tasks.index');
    Route::get('/tasks/{task}', 'show')->name('tasks.show');
});

//Statuses routes
Route::resource('task_statuses', TaskStatusController::class)
    ->except('index', 'show')
    ->middleware(['auth', 'verified']);

Route::controller(TaskStatusController::class)->group(function () {
    Route::get('/task_statuses', 'index')->name('task_statuses.index');
    Route::get('/task_statuses/{status}', 'show')->name('task_statuses.show');
});

//Labels routes
Route::resource('labels', LabelController::class)
    ->except('index', 'show')
    ->middleware(['auth', 'verified']);

Route::controller(LabelController::class)->group(function () {
    Route::get('/labels', 'index')->name('labels.index');
    Route::get('/labels/{label}', 'show')->name('labels.show');
});

require __DIR__ . '/auth.php';
