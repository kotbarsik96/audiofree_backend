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
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function updateRole(Request $request, $userId, $roleId)
    {
        if (!User::hasRight($request->cookie('user'), 'assign_role', $request))
            return RolesExceptions::noRightsResponse();

        $updaterUser = User::find($request->cookie('user'));

        $user = User::find($userId);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotExists()->getMessage()], 404);

        $newRole = Role::find($roleId);
        if (empty($newRole))
            return response(['error' => RolesExceptions::roleNotExists()->getMessage()], 404);

        $oldRole = Role::find($user->role_id);
        $oldRoleName = '"роль не задана"';
        if ($oldRole)
            $oldRoleName = $oldRole->name;

        $updaterUserRole = Role::find($updaterUser->role_id);
        if (!$updaterUserRole)
            return response(['error' => RolesExceptions::noRights()->getMessage()], 401);
        $updaterUserRolePriority = $updaterUserRole->priority;

        // пользователь с ролью, приоритет у которой выражен меньшим значением, чем у того, у кого он её меняет, не может поменять ему роль
        if ($oldRole) {
            if (!$oldRole->priority || (int) $oldRole->priority < (int) $updaterUserRolePriority)
                return response(['error' => RolesExceptions::noRights()->getMessage()], 401);
        }
        // нельзя задать роль с приоритетом, значение которого меньше или равно своему приоритету
        if ((int) $newRole->priority <= (int) $updaterUserRolePriority)
            return response(['error' => UsersExceptions::roleUpdateFail()->getMessage()], 401);

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

    public function index(Request $request, $idOrCurrent)
    {
        $user = null;
        if (is_numeric($idOrCurrent))
            $user = User::mainData()->find($idOrCurrent);
        else
            $user = User::mainData()->find($request->cookie('user'));

        if (empty($user))
            return response(['error' => AuthExceptions::userNotExists()->getMessage()], 404);

        return $user;
    }

    public function update(Request $request)
    {
        $userId = $request->cookie('user');
        $user = User::find($userId);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotExists()->getMessage()], 404);

        if (!User::authenticate($request))
            return AuthExceptions::authFaildedResponse();

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', Rule::unique('users', 'email')->ignore($userId), 'email:dns'],
            'phone_number' => ['nullable', 'string', 'regex:' . User::$phoneRegexp],
            'name' => 'required|string',
            'surname' => 'nullable|string',
            'patronymic' => 'nullable|string',
            'location' => 'nullable|string',
            'street' => 'nullable|string',
            'house' => 'nullable|string',
        ], [
            'email' => AuthExceptions::$emailFormat,
            'phone_number' => AuthExceptions::$phoneFormat,
            'name' => 'Не указано имя'
        ]);

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $user->update($validator->validated());
        return $user;
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
