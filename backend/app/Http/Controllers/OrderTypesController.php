<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderType\DeliveryType;
use App\Models\OrderType\PaymentType;

class OrderTypesController extends Controller
{
    public function getAll()
    {
        return [
            'delivery_types' => DeliveryType::select('id', 'name', 'title')->get(),
            'payment_types' => PaymentType::select('id', 'name', 'title')->get()
        ];
    }
}
