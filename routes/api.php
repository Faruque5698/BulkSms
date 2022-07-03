<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::get('send-sms',[\App\Http\Controllers\SMSController::class,'send']);

Route::post('register',[\App\Http\Controllers\API\AuthController::class,'register']);
Route::post('login',[\App\Http\Controllers\API\AuthController::class,'login']);

Route::group(["middleware" => ["auth:api"]], function(){
    Route::get('profile',[\App\Http\Controllers\API\AuthController::class,'profile']);
    Route::post('logout',[\App\Http\Controllers\API\AuthController::class,'logout']);


    Route::post('create-group',[\App\Http\Controllers\Api\GroupController::class,'create']);
    Route::post('add',[\App\Http\Controllers\Api\GroupController::class,'add']);

    Route::post('csvtojson',[\App\Http\Controllers\Api\GroupController::class,'csv']);


});

