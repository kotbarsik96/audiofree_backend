<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'email_verified_at',
        'password',
        'name',
        'surname',
        'patronymic',
        'phone_number',
        'cart_id',
        'favorite_id',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* проверяет, авторизован ли пользователь на самом деле. В случае false можно вернуть AuthExceptions::checkAuthFailed(), это разавторизует пользователя, убрав куки 'user' и 'userAdd' */
    public static function authenticate($request)
    {
        $userId = $request->cookie('user');
        $userSecret = $request->cookie('userAdd');
        if (empty($userId) || empty($userSecret))
            return false;

        $user = self::find($userId);
        if (empty($user) || !Hash::check($user->email . $user->id, $userSecret))
            return false;

        return true;
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
}