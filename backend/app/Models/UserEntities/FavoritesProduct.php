<?php

namespace App\Models\UserEntities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FavoritesProduct extends Model
{
    use HasFactory;

    public $table = 'favorites_product';
    protected $fillable = [
        'favorites_id',
        'product_id'
    ];

    public static function scopeMainData(Builder $builder)
    {
        $builder->addSelect(
            'favorites_product.product_id',
            'products.name AS product_name'
        )->leftJoin('products', 'products.id', '=', 'favorites_product.product_id');
    }
}
