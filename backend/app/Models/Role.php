<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
    public static $superRole = 'SUPER_ADMINISTRATOR';
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
        'update_rating' => ['ADMINISTRATOR', 'USER'],
        'delete_image' => ['ADMINISTRATOR', 'USER'],
    ];
    public static $allowedPages = [
        'ADMINISTRATOR' => [
            'Admin'
        ]
    ];
}