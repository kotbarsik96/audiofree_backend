<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Exceptions\AuthExceptions;
use App\Exceptions\RolesExceptions;
use App\Exceptions\UsersExceptions;
use Illuminate\Support\Facades\Validator;
use App\Filters\UsersFilter;
use App\Exceptions\CommonExceptions;

class UsersController extends Controller
{
    public function updateRole(Request $request, $roleId)
    {
        if (!User::hasRight($request->cookie('user'), 'assign_role', $request))
            return RolesExceptions::noRightsResponse();

        $validator = Validator::make($request->all(), [
            'id' => 'numeric|exists:users,id',
            'email' => 'exists:users,email|email:dns'
        ]);

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $updaterUser = User::find($request->cookie('user'));
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
            $updaterUserRole = Role::find($updaterUser->role_id);
            if (!$updaterUserRole)
                return ['error' => RolesExceptions::noRights()->getMessage()];

            $updaterUserRolePriority = $updaterUserRole->priority;

            if (!$oldRole->priority || (int) $oldRole->priority < (int) $updaterUserRolePriority)
                return ['error' => RolesExceptions::noRights()->getMessage()];
        }
        // нельзя задать роль, которая старше или равна своей
        if ((int) $roleId <= (int) $updaterUser->role_id)
            return ['error' => UsersExceptions::roleUpdateFail()->getMessage()];

        $user->update(['role_id' => $roleId]);

        $message = 'Роль пользователя ' . $user->email . ' успешно изменена: с ' . $oldRoleName . ' на ' . $newRole->name;
        return ['message' => $message];
    }

    public function filter(UsersFilter $queryFilter)
    {
        $request = $queryFilter->request;
        $limit = $request->query('limit') ?? null;
        $offset = $request->query('offset') ?? null;

        $usersQuery = User::filter($queryFilter);
        $totalCount = $usersQuery->count();

        $usersQuery->offsetLimit($limit, $offset)
            ->mainData();

        return [
            'result' => $usersQuery->get(),
            'total_count' => $totalCount
        ];
    }

    public function delete(Request $request)
    {
        if (!User::hasRight($request->cookie('user'), 'delete_user', $request))
            return RolesExceptions::noRightsResponse();

        $queries = $request->all();
        if (!array_key_exists('idsList', $queries))
            return CommonExceptions::incorrectDataResponse();

        $ids = $queries['idsList'];

        if (!is_array($ids))
            return CommonExceptions::incorrectDataResponse();

        $deleted = [];
        foreach ($ids as $id) {
            $user = User::find($id);
            if (empty($user))
                continue;

            $email = $user->email;
            $user->delete();
            array_push($deleted, $email);
        }

        return response([
            'success' => true,
            'message' => 'Удалено: ' . count($deleted) . ' из ' . count($ids)
        ]);
    }
}
