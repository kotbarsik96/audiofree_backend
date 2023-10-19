<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInfo extends Model
{
    use HasFactory;

    public $table = 'product_info';

    protected $fillable = [
        'product_id',
        'name',
        'value'
    ];
}
