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
        'quantity'
    ];

    public static function scopeMainData(Builder $builder)
    {
        $builder->addSelect(
            'cart_product.id',
            'cart_product.product_id',
            'products.name AS product_name',
            'cart_product.variations',
            'cart_product.quantity'
        )->leftJoin('products', 'products.id', '=', 'cart_product.product_id');
    }
}
