<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\BookingFileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MiscController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordResetController;

// Route publik, tidak perlu login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::get('/packages/{package}', [ServiceController::class, 'packageDetail']);
Route::get('/portfolio', [MiscController::class, 'portfolio']);

// Route yang butuh token (setelah login)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'changePassword']);

    Route::get('/bookings/available-dates', [BookingController::class, 'availableDates']);
    Route::get('/bookings/available-times', [BookingController::class, 'availableTimes']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::patch('/bookings/{booking}/confirm-price', [BookingController::class, 'confirmPrice']);

    Route::get('/bookings/{booking}/messages', [ChatController::class, 'index']);
    Route::post('/bookings/{booking}/messages', [ChatController::class, 'store']);

    Route::post('/bookings/{booking}/payments', [PaymentController::class, 'store']);
    Route::post('/bookings/{booking}/payments/check-status', [PaymentController::class, 'checkStatus']);

    Route::get('/bookings/{booking}/files', [BookingFileController::class, 'index']);
    Route::post('/bookings/{booking}/files', [BookingFileController::class, 'store']);

    Route::post('/device-tokens', [NotificationController::class, 'storeDeviceToken']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    Route::get('/bookings', [MiscController::class, 'myBookings']);


});

// Route publik, dulu dipanggil oleh server Midtrans (payment gateway).
// Sejak studio pindah ke transfer manual, endpoint ini & checkStatus di atas
// TIDAK AKTIF (lihat komentar di PaymentController), tapi routenya sengaja
// dibiarkan ada.
Route::post('/payments/callback', [PaymentController::class, 'callback']);

Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
Route::post('/resend-verification', [AuthController::class, 'resendVerification']);


Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);