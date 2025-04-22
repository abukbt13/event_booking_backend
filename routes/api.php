<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\VenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/register', [UserController::class, 'createUser']);
Route::post('auth/login', [UserController::class, 'login']);


Route::post('auth/confirmOtp',[UserController::class, 'confirmOtp']);
Route::post('auth/forget_password',[UserController::class,'forget_pass']);
Route::post('auth/finish_reset/{email_value}/{otp_value}',[UserController::class,'finish_reset']);

Route::post('payment/{booking_id}',[PaymentController::class, 'CapturePayment']);
route::get('reviews', [ReviewController::class, 'ShowAlleviews']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-auth', [UserController::class, 'auth']);
    route::post('event', [EventController::class, 'createEvent']);
    route::get('event', [EventController::class, 'showEvents']);
    route::get('event/delete/{id}', [EventController::class, 'deleteEvent']);
    route::post('event/update/{id}', [EventController::class, 'UpdateEvent']);
    route::post('event/bookings/{id}', [EventController::class, 'BookVenue']);
    route::get('booking/delete/{id}', [EventController::class, 'DeleteBooking']);

//    booking by client
    route::post('book', [BookingController::class, 'BookVenue']);
    route::get('book', [BookingController::class, 'ShowBookings']);
    route::get('book/{event_id}', [BookingController::class, 'ShowSingleBooking']);
    route::get('book/details/{book_id_id}', [BookingController::class, 'ShowBookingDetails']);
    route::post('checkout/{id}', [BookingController::class, 'CompleteCheckout']);

//    create venue by admin
    route::post('admin/venue', [VenueController::class, 'createVenue']);
    route::post('admin/venue/{id}', [VenueController::class, 'EditVenue']);
    route::get('show/venues', [VenueController::class, 'showVenues']);
    route::get('show/venues/{id}', [VenueController::class, 'showVenue']);

    route::get('admin/show/bookings', [VenueController::class, 'showBookings']);

    route::post('reviews', [ReviewController::class, 'createReview']);
    route::get('my/reviews', [ReviewController::class, 'showMyreviews']);

    route::get('client/reviews', [VenueController::class, 'showClientReviews']);

    route::get('reports', [ReportsController::class, 'Reports']);
    route::get('admin/users', [ReportsController::class, 'usersReports']);
});
