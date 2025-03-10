<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\VenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/register', [UserController::class, 'createUser']);
Route::post('auth/login', [UserController::class, 'login']);
//Route::get('test', [UserController::class, 'Notify']);

Route::post('payment',[PaymentController::class, 'CapturePayment']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-auth', [UserController::class, 'auth']);
    route::post('event', [EventController::class, 'createEvent']);
    route::get('event', [EventController::class, 'showEvents']);
    route::get('event/delete/{id}', [EventController::class, 'deleteEvent']);
    route::post('event/update/{id}', [EventController::class, 'UpdateEvent']);
    route::post('event/bookings/{id}', [EventController::class, 'BookVenue']);

//    booking by client
    route::post('book', [BookingController::class, 'BookVenue']);
    route::get('book', [BookingController::class, 'ShowBookings']);
    route::get('book/{id}', [BookingController::class, 'ShowSingleBooking']);
    route::post('checkout/{id}', [BookingController::class, 'CompleteCheckout']);

//    create venue by admin
    route::post('venue', [VenueController::class, 'createVenue']);
    route::get('venue', [VenueController::class, 'showVenues']);
});
