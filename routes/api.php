<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {

    Route::prefix('/interviews')->controller('InterviewController')->name('interviews.')->group(function () {
        Route::prefix('/{interview}')->middleware('can:view,interview')->group(function () {
            Route::get('/', 'getInterview')->name('show');

            Route::post('/start', 'startInterview')->name('start');
            Route::post('/end', 'endInterview')->name('end');
            Route::post('/submit', 'submitInterview')->name('submit');

            Route::prefix('/messages')->name('messages.')->group(function () {
                Route::get('/', 'getMessages')->name('index');
                Route::post('/', 'sendMessage')->name('create');
                Route::put('/', 'skipMessage')->name('skip');

                Route::prefix('/{message}')->group(function () {
                    Route::delete('/', 'deleteMessage')->name('delete');
                });
            });
        });
    });

});

Route::get('/test-open-ai', 'InterviewController@testOpenAI');
