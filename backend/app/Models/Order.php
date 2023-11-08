<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status_id',
        'quantity',
        'total_price',
        'is_oneclick',
        'applied_coupon',
        'name',
        'email',
        'phone_number',
        'location',
        'comment',
        'address',
        'delivery_type',
        'payment_type',
        'paid',
        'cart_rows'
    ];
}
