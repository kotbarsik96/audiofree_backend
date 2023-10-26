<?php

namespace App\Models\Taxonomies;

use App\Models\FilterableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
}
