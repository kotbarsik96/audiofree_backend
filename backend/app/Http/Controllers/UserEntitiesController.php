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
        if ($quantity < 1 || $quantity > Product::getAvailableQuantity($product, $user->id))
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

    public function updateCart(Request $request)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $updatingCart = $request->cart;
        if (!is_array($updatingCart))
            return response(['error' => 'Не передана корзина', 400]);

        $updatingCart = array_filter($updatingCart, function ($cartItem) {
            // если не передан какой-либо из ключей в $cartItem, отбросить этот элемент
            $musthaveKeys = ['id', 'product_id', 'quantity'];
            $hasKeys = array_filter(
                $musthaveKeys,
                function ($key) use ($cartItem) {
                    if (array_key_exists($key, $cartItem))
                        return true;
                    return false;
                }
            );
            if (count($musthaveKeys) === count($hasKeys))
                return true;

            return false;
        });

        /* валидация каждой ячейки */
        foreach ($updatingCart as $cartItem) {
            $row = CartProduct::find($cartItem['id']);
            if (empty($row))
                continue;

            $product = Product::find($cartItem['product_id']);
            if (empty($product))
                continue;

            /* вычислить максимальное количество, которое может быть указано в этой ячейке корзины. Для этого нужно вычесть из общего количества товаров с этим id (уменьшаемое) количество товаров из ячеек с этим же id, не включая текущую ячейку (вычитаемое, получается благодаря array_reduce) */
            $maxQuantity = $product->quantity - array_reduce($updatingCart, function ($carry, $otherCartItem) use ($cartItem) {
                if ($otherCartItem['product_id'] !== $cartItem['product_id'] || $cartItem['id'] === $otherCartItem['id'])
                    return $carry;

                return $carry + $otherCartItem['quantity'];
            }, 0);

            if ($cartItem['quantity'] > $maxQuantity)
                $cartItem['quantity'] = $maxQuantity;

            $row->update([
                'quantity' => $cartItem['quantity']
            ]);
        }

        return [
            'cart' => $this->getUserCart($request, $user->id)
        ];
    }

    public function deleteFromCart(Request $request, $cartItemId = null)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $userCart = Cart::where('user_id', $user->id)
            ->first();
        if (empty($userCart))
            return ['success' => false];

        if (empty($cartItemId)) {
            $idsList = $request->get('idsList');
            if (!is_array($idsList))
                return ['success' => false];

            foreach ($idsList as $rowId) {
                $subRequest = Request::create('/user-cart', 'DELETE', $request->all(), $request->cookie());
                $this->deleteFromCart($subRequest, $rowId);
            }

            return [
                'success' => true,
                'cart' => $this->getUserCart($request, $user->id)
            ];
        }

        $row = CartProduct::find($cartItemId);
        if (empty($row))
            return ['success' => false];

        $quantity = $row->quantity;
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
        $deletedFromCart = [];
        foreach ($productsInCart as $key => $cartRow) {
            $cartRow->variations = $this->variationsToObj($cartRow->variations);
            // если нужны все данные (например, для страницы корзины)
            if ($request->get('allData') && $request->get('allData') !== 'false') {
                $cartRow->productData = Product::mainData()->find($cartRow->product_id);
                $cartRow->productData->available_quantity = Product::getAvailableQuantity(
                    $cartRow->productData,
                    $user->id
                );

                // если в наличии товара больше нет, но он есть в корзине - удалить товар из корзины
                if ($cartRow->productData->quantity < 1) {
                    $req = Request::create('/user-cart', 'DELETE', $request->all(), $request->cookie);
                    array_push($deletedFromCart, $cartRow->productData->name);
                    $this->deleteFromCart($req, $cartRow->id);
                    unset($productsInCart->$key);
                }
                // если в наличии товара меньше, чем указано в корзине - указать 1
                elseif ($cartRow->productData->quantity < $cartRow->quantity) {
                    $cartRow->quantity = 1;
                }
            }
        }

        return [
            'cart' => $productsInCart,
            'deleted' => $deletedFromCart
        ];
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
