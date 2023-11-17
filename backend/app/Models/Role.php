<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FilterableModel;

class Role extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority'
    ];
    public static $rolesRights = [
        'add_product' => ['ADMINISTRATOR'],
        'update_product' => ['ADMINISTRATOR'],
        'delete_product' => ['ADMINISTRATOR'],
        'add_taxonomy' => ['ADMINISTRATOR'],
        'update_taxonomy' => ['ADMINISTRATOR'],
        'delete_taxonomy' => ['ADMINISTRATOR'],
        'add_variation' => ['ADMINISTRATOR'],
        'update_variation' => ['ADMINISTRATOR'],
        'delete_variation' => ['ADMINISTRATOR'],
        'assign_role' => ['ADMINISTRATOR'],
        'add_role' => [],
        'update_role' => [],
        'delete_role' => [],
        'add_rating' => ['ADMINISTRATOR', 'USER'],
        'load_image' => ['ADMINISTRATOR', 'USER'],
        'update_image' => ['ADMINISTRATOR', 'USER'],
        'update_rating' => ['ADMINISTRATOR', 'USER'],
        'delete_image' => ['ADMINISTRATOR', 'USER'],
        'upload_image_to_subpath' => ['ADMINISTRATOR'],
        'tag_image' => ['ADMINISTRATOR']
    ];
    public static $allowedPages = [
        'ADMINISTRATOR' => [
            'Admin'
        ]
    ];
}