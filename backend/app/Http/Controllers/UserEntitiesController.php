<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\UserEntitiesExceptions;
use App\Models\UserEntities\Cart;
use App\Models\UserEntities\Favorite;
use App\Models\UserEntities\FavoritesProduct;
use App\Models\User;
use App\Exceptions\AuthExceptions;
use App\Models\Products\Product;

class UserEntitiesController extends Controller
{
    public function storeToFavorites(Request $request, $productId)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $product = Product::find($productId);
        if (empty($product))
            return response(['error' => 'Товар не существует'], 400);

        $userFavorites = Favorite::where('user_id', $user->id)
            ->first();
        if (empty($userFavorites))
            Favorite::create(['user_id' => $user->id]);

        FavoritesProduct::create([
            'favorites_id' => $userFavorites->id,
            'product_id' => $productId
        ]);

        return ['success' => true];
    }

    public function deleteFromFavorites(Request $request, $productId)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $userFavorites = Favorite::where('user_id', $user->id)
            ->first();
        if (empty($userFavorites))
            return ['success' => false];
        $row = FavoritesProduct::where('product_id', $productId)
            ->where('favorites_id', $userFavorites->id)
            ->first();

        if (empty($row))
            return ['success' => false];

        $row->delete();
        return ['success' => true];
    }

    public function isInUserFavorites(Request $request, $productId)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $userFavorites = Favorite::where('user_id', $user->id)
            ->first();
        if (empty($userFavorites))
            return false;

        $row = FavoritesProduct::where('favorites_id', $userFavorites->id)
            ->where('product_id', $productId)
            ->first();
        if(empty($row))
            return false;

        return true;
    }

    public static function clearUserEntities()
    {
        $entities = [
            Cart::all(),
            Favorite::all()
        ];

        foreach ($entities as $modelsArray) {
            foreach ($modelsArray as $model) {
                $user = User::find($model->id);
                if (empty($user)) {
                    $model->delete();
                }
            }
        }
    }
}
