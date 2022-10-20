<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use GuzzleHttp\Middleware;
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
Route::resource('products', ProductController::class);

route::group(['middleware' => 'api'],function($routes){
    Route::post('register' ,[UserController::class ,'register']);
    Route::post('login' ,[UserController::class ,'login']);
    Route::post('profile' ,[UserController::class ,'profile']);
    Route::post('refresh' ,[UserController::class ,'refresh']);
    Route::post('logout' ,[UserController::class ,'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
