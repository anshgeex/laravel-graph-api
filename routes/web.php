<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains the web routes for your application. These routes are
| loaded by the RouteServiceProvider within a group which contains the "web"
| middleware group. Now create something great!
|
*/

// Authentication routes
Auth::routes();

// Routes inside the 'auth' middleware group
Route::middleware(['auth'])->group(function () {

    // Home routes
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Microsoft Graph API authentication routes
    Route::get('/getCode', [App\Http\Controllers\MicrosoftGraphController::class, 'getCode']);
    Route::get('/auth/microsoft/oauth2-callback', [App\Http\Controllers\MicrosoftGraphController::class, 'getToken']);

    // Routes inside the 'checkMicrosoftSession' middleware group
    Route::middleware(['checkMicrosoftSession'])->group(function () {

        // Microsoft Graph API user management routes
        // Route::get('/getUser', [App\Http\Controllers\MailController::class, 'getUser']);
        Route::get('/deleteUser', [App\Http\Controllers\MicrosoftGraphController::class, 'deleteUser']);
        Route::get('/getAccessToken', [App\Http\Controllers\MicrosoftGraphController::class, 'getAccessToken']);

        // Email-related routes
        Route::get('/mails', [App\Http\Controllers\MailController::class, 'mails']);
        Route::post('/submitMail', [App\Http\Controllers\MailController::class, 'sendMail']);
        Route::any('/searchMail', [App\Http\Controllers\MailController::class, 'searchMail']);
    });
});
