<?php

namespace App\Models\UserEntities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CartProduct extends Model
{
    use HasFactory;

    public $table = 'cart_product';

    protected $fillable = [
        'cart_id',
        'product_id',
        'variations',
        'quantity',
        'is_oneclick',
        'order_id',
        'purchased'
    ];

    public static function scopeMainData(Builder $builder)
    {
        $builder->addSelect(
            'cart_product.id',
            'cart_product.product_id',
            'products.name AS product_name',
            'cart_product.variations',
            'cart_product.quantity',
            'cart_product.purchased'
        )->leftJoin('products', 'products.id', '=', 'cart_product.product_id');
    }

    public static function scopeNotOrdered(Builder $builder)
    {
        $builder->whereNull('cart_product.order_id');
    }

    public static function scopeOrdered(Builder $builder)
    {
        $builder->whereNotNull('cart_product.order_id');
    }

    public function scopePurchased(Builder $builder)
    {
        $builder->where('purchased', '1');
    }

    public function scopeNotPurchased(Builder $builder)
    {
        $builder->where('purchased', '0');
    }
}
