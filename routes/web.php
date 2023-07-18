<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', 'IndexController@home')->name('home');

Route::prefix('/interviews')->controller('InterviewController')->name('interviews.')->group(function () {
    Route::get('/{interview}', 'getInterview')->middleware('signed')->name('show');
});
