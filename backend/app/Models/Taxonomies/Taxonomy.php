<?php

namespace App\Models\Taxonomies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FilterableModel;
use Illuminate\Database\Eloquent\Builder;

class Taxonomy extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'taxonomy_type',
        'name'
    ];
}
