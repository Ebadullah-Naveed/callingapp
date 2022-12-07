<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    TwilioSMSController,
    TwilioCallController,
    TwilioPhoneNumberController,
    TwilioAuthController
};

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('sendSMS', [TwilioSMSController::class, 'index']);
Route::get('makeCall', [TwilioCallController::class, 'index']);
Route::get('getPhoneNumber/{area_code}', [TwilioPhoneNumberController::class, 'index']);
Route::post('register', [TwilioAuthController::class, 'index']);
Route::post('login', [TwilioAuthController::class, 'getUser']);




