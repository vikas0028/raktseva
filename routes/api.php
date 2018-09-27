<?php

use Illuminate\Http\Request;

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

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('verify_otp', ['uses' => 'API\UserController@otpVerify']);
Route::post('resend_otp', ['uses' => 'API\UserController@otpResend']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function(){

	 Route::post('details', 'API\UserController@details');
	 Route::post('update_profile', 'API\UserController@updateProfile');
    Route::post('send_blood_request', 'API\UserController@sendBloodRequest');

 });
