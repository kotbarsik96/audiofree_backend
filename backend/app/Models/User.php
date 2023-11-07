<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\FilterableModel;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\QueryFilter;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'email_verified_at',
        'password',
        'name',
        'surname',
        'patronymic',
        'phone_number',
        'location',
        'street',
        'house',
        'cart_id',
        'favorite_id',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function scopeFilter(Builder $builder, QueryFilter $request)
    {
        $model = new FilterableModel();
        return $model->scopeFilter($builder, $request);
    }

    public function scopeOffsetLimit(Builder $query, $limit, $offset)
    {
        $model = new FilterableModel();
        return $model->scopeOffsetLimit($query, $limit, $offset);
    }

    public function scopeMainData(Builder $builder)
    {
        $builder->addSelect([
            'users.id',
            'users.email',
            'users.email_verified_at',
            'users.password',
            'users.name',
            'users.surname',
            'users.patronymic',
            'users.phone_number',
            'users.location',
            'users.street',
            'users.house',
            'roles.name AS role',
            'users.role_id',
            'users.created_at'
        ])->leftJoin('roles', 'roles.id', '=', 'users.role_id');
    }

    public static function authenticate($request)
    {
        $userId = $request->cookie('user');
        $userSecret = $request->cookie('userAdd');
        if (empty($userId) || empty($userSecret))
            return false;

        $user = self::find($userId);
        if (empty($user) || !Hash::check($user->email . $user->id, $userSecret))
            return false;

        return $user;
    }

    public static function hasRight($user, $action, $request = null)
    {
        if ($request) {
            if (!self::authenticate($request))
                return false;
        }

        if (is_numeric($user))
            $user = self::find($user);

        if (empty($user))
            return false;

        $role = Role::where('id', $user->role_id)->first();
        if (empty($role))
            return false;

        if ($role->priority === 1)
            return true;

        $roleName = $role->name;
        if (!array_key_exists($action, Role::$rolesRights))
            return false;

        if (!is_array(Role::$rolesRights[$action]))
            return false;

        return is_numeric(array_search($roleName, Role::$rolesRights[$action]));
    }

    public static function getRolePriority($userId)
    {
        $priority = 999;
        $user = User::find($userId);
        if (empty($user))
            return $priority;

        $role = Role::find($user->role_id);
        if ($role)
            $priority = (int) $role->priority;

        return $priority;
    }
}
