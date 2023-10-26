<?php

namespace App\Models\Taxonomies;

use App\Models\FilterableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStatus extends FilterableModel
{
    use HasFactory;

    public $table = 'product_statuses';

    protected $fillable = [
        'name'
    ];
}
