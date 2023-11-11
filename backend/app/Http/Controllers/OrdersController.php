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
use App\Models\Products\ProductStatistics;

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

    public function getCartRows($order)
    {
        $userCart = Cart::where('user_id', $order->user_id)->first();

        return CartProduct::where('is_oneclick', $order->is_oneclick)
            ->whereNull('order_id')
            ->where('cart_id', $userCart->id)
            ->get();
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
        $fields['status'] = 'waiting_userdata';
        $fields['paid'] = 0;

        $order = Order::where('user_id', $user->id)
            ->where('is_oneclick', $isOneClick)
            ->where('status', $fields['status'])
            ->first();

        if (empty($order))
            $order = Order::create($fields);
        else
            $order->update($fields);

        return $order->id;
    }

    public function checkout(Request $request, $orderId)
    {
        $order = $this->authenticate($request, $orderId);
        if (!($order instanceof Order))
            return $order;

        unset($order->status);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
            'phone_number' => 'required|string|regex:' . User::$phoneRegexp,
            'address' => 'required|string',
            'location' => 'required|string',
            'delivery_type' => 'required|string|exists:delivery_types,name',
            'payment_type' => 'required|string|exists:payment_types,name',
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
            'comment' => 'Комментарий',
            'location' => 'Населенный пункт',
            'delivery_type' => 'Способ доставки',
            'payment_type' => 'Способ оплаты'
        ]);

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 422);

        $fields = array_merge($validator->validated());

        $payNow = $fields['payment_type'] === 'bank_card';
        $statusName = $payNow ? 'waiting_payment' : 'in_delivery_not_paid';

        $order->update([
            'status' => $statusName,
            'name' => $fields['name'],
            'email' => $fields['email'],
            'phone_number' => $fields['phone_number'],
            'order_id' => $order->id,
            'address' => $fields['address'],
            'location' => $fields['location'],
            'delivery_type' => DeliveryType::where('name', $fields['delivery_type'])->first()->id,
            'payment_type' => PaymentType::where('name', $fields['payment_type'])->first()->id,
            'comment' => $fields['comment'],
        ]);

        $this->verifySale($order);

        return [
            'order' => $order,
            'pay_after_delivery' => $payNow ? false : true
        ];
    }

    /* удалить из корзины записи; отнять количества товаров, которые были указаны в корзине; зачесть проданное количество */
    public function verifySale($order)
    {
        $cartRows = $this->getCartRows($order);

        foreach ($cartRows as $cartRow) {
            $product = Product::mainData()->find($cartRow->product_id);
            if (empty($product)) {
                $cartRow->delete();
                continue;
            }

            ProductStatistics::updateColumns([
                'sold' => $cartRow->quantity,
                'income' => $cartRow->quantity * $product->current_price
            ], $product->id);

            $product->update([
                'quantity' => $product->quantity - $cartRow->quantity
            ]);
            $cartRow->update([
                'order_id' => $order->id,
                'purchased' => true
            ]);
        }
    }

    public function confirmPayment(Request $request, $orderId)
    {
        $order = $this->authenticate($request, $orderId);
        if (!($order instanceof Order))
            return $order;

        unset($order->status);

        $order->update([
            'paid' => $order->total_price,
            'status' => 'paid'
        ]);

        return $order;
    }
}
