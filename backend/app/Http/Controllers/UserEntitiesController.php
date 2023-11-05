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
use App\Models\UserEntities\CartProduct;

class UserEntitiesController extends Controller
{
    public $valueDelimeter = '_=_';
    public $propertyDelimeter = '|_|';

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

        $exists = FavoritesProduct::where('favorites_id', $userFavorites->id)
            ->where('product_id', $productId)
            ->first();
        if ($exists)
            return ['success' => false];

        FavoritesProduct::create([
            'favorites_id' => $userFavorites->id,
            'product_id' => $productId
        ]);

        return [
            'success' => true,
            'favorites' => $this->getUserFavorites($request, $user->id)
        ];
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
        return [
            'success' => true,
            'favorites' => $this->getUserFavorites($request, $user->id)
        ];
    }

    public function getUserFavorites(Request $request, $userId = null)
    {
        $user = null;
        if (empty($userId))
            $user = User::authenticate($request);
        else
            $user = User::find($userId);

        if (empty($user))
            return response(['error' => AuthExceptions::userNotExists()->getMessage()], 400);

        $userFavorites = Favorite::where('user_id', $user->id)->first();
        if (empty($userFavorites))
            Favorite::create(['user_id' => $user->id]);

        $productsInFavorite = FavoritesProduct::mainData()
            ->where('favorites_id', $userFavorites->id)
            ->get();

        return $productsInFavorite;
    }

    public function storeToCart(Request $request, $productId)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $product = Product::find($productId);
        if (empty($product))
            return response(['error' => 'Товар не существует'], 400);

        $userCart = Cart::where('user_id', $user->id)
            ->first();
        if (empty($userCart))
            Cart::create(['user_id' => $user->id]);

        $variations = $request->variations;
        if (!is_array($variations))
            return response(['error' => 'Произошла ошибка при обработке параметров товара'], 400);

        $quantity = (int) $request->quantity ?? 1;
        if ($quantity < 1 || $quantity > 99)
            return response(['error' => 'Передано некорректное количество'], 400);

        CartProduct::create([
            'cart_id' => $userCart->id,
            'product_id' => $productId,
            'variations' => $this->variationsToString($variations),
            'quantity' => $quantity
        ]);

        return [
            'success' => true,
            'cart' => $this->getUserCart($request, $user->id)
        ];
    }

    public function deleteFromCart(Request $request, $productId)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $userCart = Cart::where('user_id', $user->id)
            ->first();
        if (empty($userCart))
            return ['success' => false];

        $row = CartProduct::where('product_id', $productId)
            ->where('cart_id', $userCart->id)
            ->first();
        if (empty($row))
            return ['success' => false];

        $row->delete();
        return [
            'success' => true,
            'cart' => $this->getUserCart($request, $user->id)
        ];
    }

    public function getUserCart(Request $request, $userId = null)
    {
        $user = null;
        if (empty($userId))
            $user = User::authenticate($request);
        else
            $user = User::find($userId);

        if (empty($user))
            return response(['error' => AuthExceptions::userNotExists()->getMessage()], 400);

        $userCart = Cart::where('user_id', $user->id)->first();
        if (empty($userCart))
            Cart::create(['user_id' => $user->id]);

        $productsInCart = CartProduct::mainData()
            ->where('cart_id', $userCart->id)
            ->get();
        foreach ($productsInCart as $productData) {
            $productData->variations = $this->variationsToObj($productData->variations);
        }

        return $productsInCart;
    }

    public function variationsToString($variations)
    {
        $string = '';
        foreach ($variations as $data) {
            $name = array_key_exists('name', $data) ? $data['name'] : null;
            $value = array_key_exists('value', $data) ? $data['value'] : null;
            if (empty($name) || empty($value))
                continue;

            $string .= $name . $this->valueDelimeter . $value . $this->propertyDelimeter;
        }
        $limit = strlen($this->propertyDelimeter) * (-1);
        $string = substr($string, 0, $limit);
        return $string;
    }

    public function variationsToObj($variations)
    {
        $obj = [];
        foreach (explode($this->propertyDelimeter, $variations) as $property) {
            $keyAndValue = explode($this->valueDelimeter, $property);
            $subObj = ['name' => $keyAndValue[0], 'value' => $keyAndValue[1]];
            array_push($obj, $subObj);
        }
        return $obj;
    }
}
