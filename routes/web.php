<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/verify-otp', 'Auth\RegisterController@create');

Route::get('/register-form', 'Auth\RegisterController@registerForm');

Route::get('/get-form', 'Auth\RegisterController@getData');

Route::post('/verify', 'Auth\RegisterController@verifyOTP');

Route::post('/update-profile', 'HomeController@updateProfile');





