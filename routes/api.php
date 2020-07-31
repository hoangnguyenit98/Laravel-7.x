<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('register', 'JWTAuthController@register');
    Route::post('login', 'JWTAuthController@login');
    Route::post('logout', 'JWTAuthController@logout');
    Route::post('refresh', 'JWTAuthController@refresh');
    Route::get('profile', 'JWTAuthController@profile');
});

Route::resource('user', 'UserController')->except(['create', 'edit']);

Route::post('user/reset-password', 'UserController@resetPassword');
Route::post('user/change-password', 'UserController@changePassword');
Route::post('user/export-csv', 'UserController@exportCsv');

Route::get('send-mail', function () {
    $details = [
        'title' => 'Mail from ItSolutionStuff.com',
        'body' => 'This is for testing email using smtp',
    ];

    \Mail::to('hoangnguyenit98@gmail.com')->send(new \App\Mail\MyTestMail($details));

    dd("Email is Sent.");
});
