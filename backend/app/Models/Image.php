<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'size_kb',
        'width',
        'height',
        'user_id',
    ];

    // вернет список названий таблиц, у которых есть колонка "image_id"
    public static function getTablesWhereExist()
    {
        return [
            'product_images',
            'products',
            // 'users', позже будет добавлено
        ];
    }
}