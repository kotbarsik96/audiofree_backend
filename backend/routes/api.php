<?php

use App\Http\Controllers\ImagesController;
use App\Http\Controllers\RatingsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\TaxonomiesController;
use App\Http\Controllers\UserEntitiesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Products\ProductsController;

Route::post('/user-entities/create/cart', [UserEntitiesController::class, 'storeCart']);
Route::post('/user-entities/create/favorite', [UserEntitiesController::class, 'storeFavorite']);

Route::get('/product/{id}', [ProductsController::class, 'index']);
Route::get('/products', [ProductsController::class, 'filter']);

Route::get('/taxonomies', [TaxonomiesController::class, 'all']);

Route::get('/roles/check/page-access', [RolesController::class, 'checkPageAccess']);

Route::get('/auth/check', [AuthController::class, 'checkAuth']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'sendEmailVerification']);
    Route::get('/email/verify/{code}', [AuthController::class, 'verifyEmail']);

    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    Route::post('/rating/set/{productId}/{ratingValue}', [RatingsController::class, 'store']);
    Route::delete('/rating/delete/{productId}', [RatingsController::class, 'delete']);

    Route::post('/image/load', [ImagesController::class, 'handleStoreRequest']);
    Route::post('/image/update/{id}', [ImagesController::class, 'update']);
    Route::delete('/image/delete/{id}', [ImagesController::class, 'delete']);
    Route::delete('/image/delete', [ImagesController::class, 'delete']);

    // админские привилегии
    Route::post('/users/update/role/{roleId}', [UsersController::class, 'updateRole']);

    Route::post('/roles/create', [RolesController::class, 'store']);
    Route::post('/roles/update/{id}', [RolesController::class, 'update']);
    Route::delete('/roles/delete/{id}', [RolesController::class, 'delete']);

    Route::post('/taxonomy/create/{taxName}', [TaxonomiesController::class, 'storeOrUpdate']);
    Route::post('/taxonomy/update/{taxName}/{id}', [TaxonomiesController::class, 'storeOrUpdate']);
    Route::delete('/taxonomy/delete/{taxName}/{id}', [TaxonomiesController::class, 'delete']);

    Route::post('/product/create', [ProductsController::class, 'store']);
    Route::post('/product/update/{id}', [ProductsController::class, 'update']);
    Route::delete('/product/delete/{id}', [ProductsController::class, 'handleDelete']);
    Route::delete('/product/delete', [ProductsController::class, 'handleDelete']);
});