<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'quantity',
        'total_price',
        'type',
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
    ];
}
