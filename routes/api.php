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

Route::get('cache-clear',[\App\Http\Controllers\SMSController::class,'cache']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::get('send-sms',[\App\Http\Controllers\SMSController::class,'send']);

Route::post('register',[\App\Http\Controllers\API\AuthController::class,'register']);
Route::post('login',[\App\Http\Controllers\API\AuthController::class,'login']);
Route::post('otp-send',[\App\Http\Controllers\API\AuthController::class,'send_otp']);
Route::post('otp-check',[\App\Http\Controllers\API\AuthController::class,'otp_check']);
Route::post('pin-set',[\App\Http\Controllers\API\AuthController::class,'pin_set']);



Route::group(["middleware" => ["auth:api","active"]], function(){
    Route::get('profile',[\App\Http\Controllers\API\AuthController::class,'profile']);
    Route::post('logout',[\App\Http\Controllers\API\AuthController::class,'logout']);


    Route::post('create-group',[\App\Http\Controllers\Api\GroupController::class,'create']);
    Route::post('add',[\App\Http\Controllers\Api\GroupController::class,'add']);

    Route::get('group_list',[\App\Http\Controllers\Api\GroupController::class,'list']);
    Route::get('group/delete/{id}',[\App\Http\Controllers\Api\GroupController::class,'delete']);

    Route::get('delete/{id}',[\App\Http\Controllers\Api\GroupController::class,'contact_delete']);


    Route::get('group_contact/{id}',[\App\Http\Controllers\Api\GroupController::class,'group_contact']);

    Route::post('add_template',[\App\Http\Controllers\Api\TemplateController::class,'add_template']);
    Route::get('template/list',[\App\Http\Controllers\Api\TemplateController::class,'list']);
    Route::get('template/delete/{id}',[\App\Http\Controllers\Api\TemplateController::class,'delete']);

    Route::post('csvtojson',[\App\Http\Controllers\Api\GroupController::class,'csv']);

    Route::post('send-sms',[\App\Http\Controllers\SMSController::class,'send']);
    Route::post('nonmusk/send-sms',[\App\Http\Controllers\SMSController::class,'send2']);

    Route::get('muskList',[\App\Http\Controllers\SMSController::class,'muskList']);
    Route::get('nonmuskList',[\App\Http\Controllers\SMSController::class,'nonmuskList']);

    Route::post('kyc',[\App\Http\Controllers\API\AuthController::class,'kyc']);
    Route::get('kyc/list',[\App\Http\Controllers\API\AuthController::class,'kyc_list']);



});

