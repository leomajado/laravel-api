<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;

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

Route::post('/login', [AuthController::class,'login'])->name('login');
Route::post('/signup',[AuthController::class,'signup'])->name('signup');

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('/logout', [AuthController::class,'logout']);
    Route::get('/user', [AuthController::class,'user']);

    Route::get('/posts', [PostController::class,'getAll']);
    Route::get('/post/{id}', [PostController::class,'getPostById']);
    Route::get('/user/{id}/posts', [PostController::class,'getAllByUser']);
    Route::post('/post',[PostController::class,'store']);
    Route::put('/post/{id}',[PostController::class,'update']);
    Route::delete('/post/{id}',[PostController::class,'destroy']);

});
