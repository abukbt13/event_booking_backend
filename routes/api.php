<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/register', [UserController::class, 'createUser']);
Route::post('auth/login', [UserController::class, 'login']);
//Route::get('test', [UserController::class, 'Notify']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-auth', [UserController::class, 'auth']);
    route::post('event', [EventController::class, 'createEvent']);
    route::get('event', [EventController::class, 'showEvents']);
});
