<?php

namespace App\Models\Taxonomies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxonomyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title'
    ];

    protected $table = 'taxonomies_types';
}
