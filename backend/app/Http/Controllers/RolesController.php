<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\RolesExceptions;
use App\Models\Role;
use Illuminate\Validation\Rule;

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

    public function validateRequest($request, $ignoreId = null)
    {
        return Validator::make($request->all(), [
            // 'name' => 'required|string|unique:roles'
            'name' => ['string', Rule::unique('roles', 'name')->ignore($ignoreId), 'required']
        ], [
            'name.required' => 'Не указано название роли',
            'name.unique' => 'Роль с таким названием уже существует'
        ]);
    }

    public function store(Request $request)
    {
        $rightCheck = AuthController::checkUserRight($request, 'add_role');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $validator = $this->validateRequest($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $role = Role::create($validator->validated());
        return $role;
    }

    public function update(Request $request, $id)
    {
        $rightCheck = AuthController::checkUserRight($request, 'update_role');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $validator = $this->validateRequest($request, $id);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $role = Role::find($id);
        if (empty($role))
            return response(['error' => RolesExceptions::roleNotExists()->getMessage()], 400);

        $role->update($validator->validated());
        return $role;
    }

    public function delete(Request $request, $id)
    {
        $rightCheck = AuthController::checkUserRight($request, 'delete_role');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $role = Role::find($id);
        if (empty($role))
            return response(['error' => RolesExceptions::roleNotExists()->getMessage()], 400);

        $roleName = $role->name;

        $role->delete();
        return ['success' => true, 'message' => 'Успешно удалено: роль ' . $roleName];
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