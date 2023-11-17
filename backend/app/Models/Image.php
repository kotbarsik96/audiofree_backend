<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\FilterableModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

class Image extends FilterableModel
{
    use HasFactory;

    protected $fillable = [
        'path', // images/products/
        'name', // be5ba3ba_4,
        'original_name', // airdots1,
        'tag',
        'extension',
        'size_kb',
        'width',
        'height',
        'user_id',
    ];

    protected static $forbiddenSubpaths = [
        'images/products/'
    ];

    public static function isForbiddenSubpath($subpath)
    {
        foreach (self::$forbiddenSubpaths as $forbiddenSubpath) {
            if (str_contains($subpath, $forbiddenSubpath))
                return true;
        }

        return false;
    }

    // вернет список названий таблиц, у которых есть колонка "image_id"
    public static function getTablesWhereExist()
    {
        return [
            'product_images',
            'products',
        ];
    }

    public static function scopeNotForbidden(Builder $builder)
    {
        foreach (self::$forbiddenSubpaths as $forbiddenSubpath) {
            $builder->where('path', 'not like', '%' . $forbiddenSubpath . '%');
        }
    }

    public static function scopeFullpath(Builder $builder)
    {
        $builder->addSelect(
            DB::raw('concat(images.path, images.name) as image_path'),
            DB::raw('images.extension as extension')
        );
    }

    public static function scopeSeparatePathNameExtension(Builder $builder)
    {
        $builder->addSelect(
            'images.path',
            'images.name',
            'images.extension'
        );
    }

    public static function scopeForGallery(Builder $builder, Request $request = null)
    {
        $userId = $request ? (int) $request->cookie('user') : 0;
        $user = User::find($userId);
        $role = empty($user) ? null : Role::where('id', $user->role_id)->first();

        $priority = $role ? $role->priority : 999;

        $builder->fullPath()
            ->addSelect(
                'images.id',
                'images.original_name',
                'users.email as uploader_email',
                DB::raw("CASE 
                WHEN roles.priority > $priority THEN 1
                WHEN images.user_id = $userId THEN 1
                ELSE 0
                END AS can_modify
            ")
            )->leftJoin('users', 'users.id', '=', 'images.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id');
    }
}
