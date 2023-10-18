<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\RolesExceptions;
use App\Models\Role;
use Illuminate\Validation\Rule;
use App\Http\Controllers\AuthController;

class RolesController extends Controller
{
    public function validateRequest($request, $ignoreId = null)
    {
        return Validator::make($request->all(), [
            'name' => ['string', Rule::unique('roles', 'name')->ignore($ignoreId), 'required'],
            'priority' => ['numeric']
        ], [
            'name.required' => 'Не указано название роли',
            'name.unique' => 'Роль с таким названием уже существует',
            'priority.numeric' => 'Приоритет роли должен быть числовым'
        ]);
    }

    public static function getNotExistingPriority()
    {
        $priority = 5;
        while (Role::where('priority', $priority)->first()) {
            $priority++;
        }
        return $priority;
    }

    public function store(Request $request)
    {
        $rightCheck = AuthController::checkUserRight($request, 'add_role');
        if (!$rightCheck['has_right'])
            return RolesExceptions::noRightsResponse();

        $validator = $this->validateRequest($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $fields = $validator->validated();
        if (!array_key_exists('priority', $fields))
            $fields['priority'] = $this->getNotExistingPriority();
        if (!$fields['priority'] || $fields['priority'] <= 0)
            $fields['priority'] = $this->getNotExistingPriority();

        $role = Role::create($fields);
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

        if (!array_key_exists($action, Role::$rolesRights)) {
            return [
                'has_right' => false,
                'error' => RolesExceptions::actionNotExists()->getMessage()
            ];
        }

        if ($role === Role::$superRole)
            return ['has_right' => true, 'error' => false];

        $allowed = Role::$rolesRights[$action];
        return [
            'has_right' => is_numeric(array_search($role, $allowed)),
            'error' => RolesExceptions::noRights()->getMessage()
        ];
    }

    /* если указать в $request->query('noError'), будет возвращен обычный ответ, без ошибки */
    public function checkPageAccess(Request $request)
    {
        $authController = new AuthController();
        $checkedAuth = $authController->checkAuth($request, true);
        $user = null;

        if (!is_array($checkedAuth)) {
            $noError = $request->query('noError');
            if (empty($noError || $noError === 'false'))
                return $checkedAuth;

            return response(['success' => false, 'error' => true]);
        }

        $user = $checkedAuth['user'];

        $userRole = Role::find($user->role_id);
        $userRoleName = $userRole->name;

        if ($userRoleName === Role::$superRole)
            return response(['success' => true, 'error' => false]);

        $keyExists = array_key_exists($userRoleName, Role::$allowedPages);
        $allowedPages = $keyExists
            ? Role::$allowedPages[$userRoleName] : [];
        if (is_numeric(array_search($request->query('page'), $allowedPages)))
            return response(['success' => true, 'error' => false]);
    }
}