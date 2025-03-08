<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyStartPageController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/my-start-page', [MyStartPageController::class, 'show']);