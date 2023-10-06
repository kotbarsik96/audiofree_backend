<?php

use App\Http\Controllers\RolesController;
use App\Http\Controllers\TaxonomiesController;
use App\Http\Controllers\UserEntitiesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProductsController;

Route::post('/user-entities/create/cart', [UserEntitiesController::class, 'storeCart']);
Route::post('/user-entities/create/favorite', [UserEntitiesController::class, 'storeFavorite']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/check', [AuthController::class, 'checkAuth']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::post('/users/update/role/{roleId}', [UsersController::class, 'updateRole']);

    Route::post('/roles/create', [RolesController::class, 'store']);

    Route::post('/taxonomy/create/{taxName}', [TaxonomiesController::class, 'storeOrUpdate']);
    Route::post('/taxonomy/update/{taxName}/{id}', [TaxonomiesController::class, 'storeOrUpdate']);
    Route::post('/taxonomy/delete/{taxName}/{id}', [TaxonomiesController::class, 'delete']);

    Route::post('/products/create', [ProductsController::class, 'store']);
    Route::post('/products/update/{id}', [ProductsController::class, 'update']);
    Route::post('/products/delete/{id}', [ProductsController::class, 'delete']);

    Route::post('/rating/set/{productId}/{ratingValue}');
// });