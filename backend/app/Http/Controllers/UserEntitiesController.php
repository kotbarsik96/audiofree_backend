<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\UserEntitiesExceptions;
use App\Models\UserEntities\Cart;
use App\Models\UserEntities\Favorite;

class UserEntitiesController extends Controller
{
    public function storeCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ], UserEntitiesExceptions::storeUserEntitiesValidator());

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $cart = Cart::create($validator->validated());
        return $cart;
    }

    public function storeFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ], UserEntitiesExceptions::storeUserEntitiesValidator());

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $favorites = Favorite::create($validator->validated());
        return $favorites;
    }
}