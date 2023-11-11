<?php

namespace App\Models\Taxonomies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FilterableModel;

class Taxonomy extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'taxonomy_type',
        'name'
    ];
}
