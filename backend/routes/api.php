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
use App\Models\UserEntities\CartProduct;

Route::get('/product/{id}', [ProductsController::class, 'index']);
Route::get('/products', [ProductsController::class, 'filter']);

Route::get('/taxonomies', [TaxonomiesController::class, 'all']);
Route::get('/taxonomies/{taxonomyTitle}', [TaxonomiesController::class, 'filter']);

Route::get('/roles/check/page-access', [RolesController::class, 'checkPageAccess']);

Route::get('/users', [UsersController::class, 'filter']);
Route::get('/user/{idOrCurrent}', [UsersController::class, 'index']);

Route::get('/auth/check', [AuthController::class, 'checkAuth']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-favorites', [UserEntitiesController::class, 'getUserFavorites']);
    Route::post('/user-favorites/{productId}', [UserEntitiesController::class, 'storeToFavorites']);
    Route::delete(
        '/user-favorites/{productId}',
        [UserEntitiesController::class, 'deleteFromFavorites']
    );

    Route::get('/user-cart', [UserEntitiesController::class, 'getUserCart']);
    Route::post('/user-cart/update', [UserEntitiesController::class, 'updateCart']);
    Route::get('/user-cart/{userId}', [UserEntitiesController::class, 'getUserCart']);
    Route::post('/user-cart/{productId}', [UserEntitiesController::class, 'storeToCart']);
    Route::delete('/user-cart', [UserEntitiesController::class, 'deleteFromCart']);
    Route::delete('/user-cart/{cartItemId}', [UserEntitiesController::class, 'deleteFromCart']);

    Route::get('/email/verify', [AuthController::class, 'sendEmailVerification']);
    Route::get('/email/verification-sent', [AuthController::class, 'isVerificationSent']);
    Route::get('/email/verify/{code}', [AuthController::class, 'verifyEmail']);

    Route::delete('/user/delete/{id}', [AuthController::class, 'delete']);
    Route::delete('/users/delete', [UsersController::class, 'delete']);
    Route::post('/user/update', [UsersController::class, 'update']);

    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    Route::post('/rating/set/{productId}/{ratingValue}', [RatingsController::class, 'store']);
    Route::delete('/rating/delete/{productId}', [RatingsController::class, 'delete']);
    Route::get('/rating/product-user/{productId}', [RatingsController::class, 'usersProductRating']);
    Route::get('/rating/product-user/{productId}/{userId}', [RatingsController::class, 'usersProductRating']);


    Route::post('/image/load', [ImagesController::class, 'handleStoreRequest']);
    Route::post('/image/update/{id}', [ImagesController::class, 'update']);
    Route::delete('/image/delete/{id}', [ImagesController::class, 'delete']);
    Route::delete('/image/delete', [ImagesController::class, 'delete']);

    // админские привилегии
    Route::post('/users/update/role/{userId}/{roleId}', [UsersController::class, 'updateRole']);

    Route::get('/roles', [RolesController::class, 'filter']);
    Route::post('/roles/create', [RolesController::class, 'store']);
    Route::post('/roles/update/{id}', [RolesController::class, 'update']);
    Route::delete('/roles/delete/{id}', [RolesController::class, 'handleDelete']);
    Route::delete('/roles/delete', [RolesController::class, 'handleDelete']);

    Route::post('/taxonomy/create/{taxName}', [TaxonomiesController::class, 'storeOrUpdate']);
    Route::post('/taxonomy/update/{taxName}/{id}', [TaxonomiesController::class, 'storeOrUpdate']);
    Route::delete('/taxonomy/delete/{taxName}/{id}', [TaxonomiesController::class, 'handleDelete']);
    Route::delete('/taxonomy/delete/{taxName}/', [TaxonomiesController::class, 'handleDelete']);

    Route::post('/product/create', [ProductsController::class, 'store']);
    Route::post('/product/update/{id}', [ProductsController::class, 'update']);
    Route::delete('/product/delete/{id}', [ProductsController::class, 'handleDelete']);
    Route::delete('/product/delete', [ProductsController::class, 'handleDelete']);
});
