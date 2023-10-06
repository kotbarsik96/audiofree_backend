<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\RolesExceptions;
use App\Models\Role;

class RolesController extends Controller
{
    protected static $super_role = 'SUPER_ADMINISTRATOR';
    protected static $rolesRights = [
        'add_product' => ['ADMINISTRATOR'],
        'update_product' => ['ADMINISTRATOR'],
        'delete_product' => ['ADMINISTRATOR'],
        'add_taxonomy' => ['ADMINISTRATOR'],
        'update_taxonomy' => ['ADMINISTRATOR'],
        'delete_taxonomy' => ['ADMINISTRATOR'],
        'update_role' => ['ADMINISTRATOR'],
        'add_variation' => ['ADMINISTRATOR'],
        'update_variation' => ['ADMINISTRATOR'],
        'delete_variation' => ['ADMINISTRATOR'],
    ];

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles'
        ], RolesExceptions::storeValidator());

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $role = Role::create($validator->validated());
        return $role;
    }

    /* returns array: ['has_right' => false|true, 'error' => false|Exception $e] 
    role - id or string
    */
    public static function checkRoleRight($role, $action)
    {
        $role = Role::where('id', $role)
            ->orWhere('name', $role)
            ->first();

        if (empty($role)) {
            return [
                'has_right' => false,
                'error' => RolesExceptions::roleNotExists()->getMessage()
            ];
        }

        $role = $role->name;

        if (!array_key_exists($action, self::$rolesRights)) {
            return [
                'has_right' => false,
                'error' => RolesExceptions::actionNotExists()->getMessage()
            ];
        }

        if ($role === self::$super_role)
            return ['has_right' => true, 'error' => false];

        $allowed = self::$rolesRights[$action];
        return [
            'has_right' => is_numeric(array_search($role, $allowed)),
            'error' => RolesExceptions::noRights()->getMessage()
        ];
    }
}