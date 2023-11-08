<?php

namespace App\Http\Controllers;

use App\Exceptions\OrdersExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\User;
use App\Exceptions\AuthExceptions;
use App\Models\OrderStatus;
use App\Models\OrderType\DeliveryType;
use App\Models\OrderType\PaymentType;
use App\Models\Products\Product;
use App\Models\UserEntities\Cart;
use App\Models\UserEntities\CartProduct;
use App\Exceptions\CommonExceptions;
use App\Exceptions\RolesExceptions;

class OrdersController extends Controller
{
    public function authenticate(Request $request, $orderId)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 403);

        $order = Order::find($orderId);
        if (empty($order))
            return CommonExceptions::pageNotFoundResponse();

        $isAuthenticated = (int) $order->user_id === (int) $user->id;
        if (empty($isAuthenticated))
            return RolesExceptions::noRightsResponse();

        return $order;
    }

    public function storeNew(Request $request)
    {
        $user = User::authenticate($request);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotLoggedIn()->getMessage()], 401);

        $userCart = Cart::where('user_id', $user->id)->first();
        if (empty($userCart)) {
            Cart::create(['user_id' => $user->id]);
            return OrdersExceptions::emptyCartResponse();
        }
        $isOneClick = $request->isOneClick ? '1' : '0';
        $productsInCart = CartProduct::where('cart_id', $userCart->id)
            ->where('is_oneclick', $isOneClick)
            ->get();

        $cartRows = '';
        $quantity = 0;
        $totalPrice = 0;
        foreach ($productsInCart as $cartRow) {
            $product = Product::currentPrice()->find($cartRow->product_id);
            if (empty($product))
                continue;

            $cartRows .= $cartRow->id . ',';
            $quantity += $cartRow->quantity;
            $totalPrice += $product->current_price * $cartRow->quantity;
        }
        if (empty($quantity))
            return OrdersExceptions::emptyCartResponse();
        $cartRows = substr($cartRows, 0, strlen($cartRows) - 1);

        $address = [
            $user->location ?? '',
            $user->street ?? '',
            $user->house ?? ''
        ];
        $address = implode(', ', array_filter($address, fn ($str) => !empty($str)));
        $fields = [
            'user_id' => $user->id,
            'cart_rows' => $cartRows,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'is_oneclick' => $isOneClick,
            'name' => $user->name,
            'email' => $user->email,
            'location' => $user->location ?? null,
            'address' => $address,
            'phone_number' => $user->phone_number ?? null
        ];
        $fields['status_id'] = OrderStatus::where('name', 'waiting_userdata')->first()->id;
        $fields['paid'] = 0;

        $order = Order::where('user_id', $user->id)
            ->where('is_oneclick', $isOneClick)
            ->where('status_id', $fields['status_id'])
            ->first();

        if (empty($order))
            $order = Order::create($fields);        
        else
            $order->update($fields);

        return $order->id;
    }

    public function updateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
            'phone_number' => 'required|string|regex:' . User::$phoneRegexp,
            'address' => 'required|string',
            'location' => 'required|string',
            'delivery_type' => 'required|string|exists:delivery_types,name',
            'payment_type' => 'required|string|exists:payment_types,name',
            'applied_coupon' => 'nullable|string',
            'comment' => 'nullable|string'
        ], [
            'required' => 'Не указано: :attribute',
            'string' => 'Неверно указано: :attribute',
            'phone_number.regex' => AuthExceptions::$phoneFormat,
            'delivery_type.exists' => 'Способ доставки недоступен',
            'payment_type.exists' => 'Способ оплаты недоступен',
            'applied_coupon' => 'Купон недействителен'
        ], [
            'name' => 'Имя',
            'email' => 'Email',
            'phone_number' => 'Номер телефона',
            'address' => 'Адрес',
            'location' => 'Населенный пункт',
            'delivery_type' => 'Способ доставки',
            'payment_type' => 'Способ оплаты'
        ]);

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 422);

        $fields = $validator->validated();
        $fields['delivery_type'] = DeliveryType::where('name', $fields['delivery_type'])->first();
        $fields['payment_type'] = PaymentType::where('name', $fields['payment_type'])->first();
    }
}
