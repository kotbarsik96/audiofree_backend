<?php

namespace App\Models\Taxonomies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomiesMeta extends Model
{
    use HasFactory;

    protected $table = 'taxonomies_meta';

    protected $fillable = [
        'taxonomy_type',
        'taxonomy_name',
        'meta_name',
        'meta_value',
    ];
}
