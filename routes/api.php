<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

Route::post('login', [AuthController::class,'login']);
Route::post('signup',[AuthController::class,'signup']);

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('logout', [AuthController::class,'logout']);
    Route::get('user', [AuthController::class,'user']);

});
