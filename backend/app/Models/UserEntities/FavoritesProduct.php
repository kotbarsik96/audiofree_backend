<?php

namespace App\Models\UserEntities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoritesProduct extends Model
{
    use HasFactory;

    public $table = 'favorites_product';
    protected $fillable = [
        'favorites_id',
        'product_id'
    ];
}
