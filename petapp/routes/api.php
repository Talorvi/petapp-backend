<?php

use App\Http\Controllers\API\AvatarController;
use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::middleware(['auth:api', 'api.custom'])->group(function () {
    /**
     * Users
     */
    Route::get('/user/me', [UserController::class, 'showAuthenticatedUser']);
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/user/{user}', [UserController::class, 'update']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);

    /**
     * Avatars
     */
    Route::post('/user/avatar', [AvatarController::class, 'updateAvatar']);
    Route::delete('/user/avatar', [AvatarController::class, 'removeAvatar']);

    /**
     * Offers
     */
    Route::post('/offers', [OfferController::class, 'store']);
    Route::put('/offers/{offer}', [OfferController::class, 'update']);
    Route::delete('/offers/{offer}', [OfferController::class, 'destroy']);
    Route::post('/offers/{offer}/ratings', [OfferController::class, 'storeRating']);
    Route::put('/ratings/{rating}', [OfferController::class, 'updateRating']);
    Route::delete('/ratings/{rating}', [OfferController::class, 'destroyRating']);

    Route::post('/offers/{offer}/images', [OfferController::class, 'uploadImage']);
    Route::delete('/offers/{offer}/images/{image}', [OfferController::class, 'deleteImage']);

});

// api.custom -> public API
Route::middleware('api.custom')->group(function () {
    /**
     * Authentication
     */
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    /**
     * Offers
     */
    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/{id}', [OfferController::class, 'show']);
    Route::get('/offers/{offer}/ratings', [OfferController::class, 'getRatings']);

    /**
     * Users
     */
    Route::get('/user/{user}', [UserController::class,'show']);

});
