<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Exceptions\AuthExceptions;
use App\Exceptions\RolesExceptions;
use App\Exceptions\UsersExceptions;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AuthController;

class UsersController extends Controller
{
    public function updateRole(Request $request, $roleId)
    {
        $updaterCheck = AuthController::checkUserRight($request, 'assign_role');
        if (!$updaterCheck['has_right'])
            return ['error' => RolesExceptions::noRights()->getMessage()];

        $authController = new AuthController();
        // checkAuth уже был пройден в checkUserRight, поэтому можно спокойно сразу получить 'user'
        $updaterUser = $authController->checkAuth($request, true)['user'];

        $validator = Validator::make($request->all(), [
            'id' => 'numeric|exists:users,id',
            'email' => 'exists:users,email|email:dns'
        ], );

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $fields = $validator->validated();
        $userIdOrEmail = array_key_exists('id', $fields) ? $fields['id'] : null;
        if (!$userIdOrEmail)
            $userIdOrEmail = array_key_exists('email', $fields) ? $fields['email'] : null;

        $user = User::where('id', $userIdOrEmail)
            ->orWhere('email', $userIdOrEmail)
            ->first();

        if (empty($user))
            return ['error' => AuthExceptions::userNotExists()->getMessage()];

        $newRole = Role::find($roleId);
        if (empty($newRole))
            return ['error' => RolesExceptions::roleNotExists()->getMessage()];

        $oldRole = Role::find($user->role_id);
        $oldRoleName = '"не задано"';
        if ($oldRole)
            $oldRoleName = $oldRole->name;

        // пользователь с ролью ниже, чем у того, у кого он её меняет, не может поменять ему роль
        if ($oldRole) {
            if ((int) $oldRole->id < (int) $updaterUser->role_id)
                return ['error' => RolesExceptions::noRights()->getMessage()];
        }
        // нельзя задать роль, которая старше или равна своей
        if ((int) $roleId <= (int) $updaterUser->role_id)
            return ['error' => UsersExceptions::roleUpdateFail()->getMessage()];

        $user->update(['role_id' => $roleId]);

        $message = 'Роль пользователя ' . $user->email . ' успешно изменена: с ' . $oldRoleName . ' на ' . $newRole->name;
        return ['message' => $message];
    }
}