<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\RolesExceptions;
use App\Models\Role;
use Illuminate\Validation\Rule;
use App\Exceptions\AuthExceptions;
use App\Models\User;
use App\Filters\RolesFilter;

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

    public function filter(RolesFilter $queryFilter)
    {
        $request = $queryFilter->request;
        $limit = $request->query('limit') ?? null;
        $offset = $request->query('offset') ?? null;

        $filtered = Role::filter($queryFilter)
            ->select(['id', 'name', 'priority']);
        $count = $filtered->count();
        return [
            'result' => $filtered->offsetLimit($limit, $offset)->get(),
            'total_count' => $count
        ];
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
        if (!User::hasRight($request->cookie('user'), 'add_role', $request))
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
        if (!User::hasRight($request->cookie('user'), 'update_role', $request))
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

    public function handleDelete(Request $request, $id = null)
    {
        if (!User::hasRight($request->cookie('user'), 'delete_role', $request))
            return RolesExceptions::noRightsResponse();

        if (is_numeric($id)) {
            $res = $this->delete($id);
            $code = array_key_exists('code', $res) ? $res['code'] ?? 200 : 200;
            unset($res['code']);
            return response($res, $code);
        } else {
            $deleted = [];
            $errors = [];
            $queries = $request->all();
            $list = array_key_exists('idsList', $queries) ? $queries['idsList'] : [];
            foreach ($list as $id) {
                $res = $this->delete($id);
                if ($res['error'])
                    array_push($errors, $res['error']);
                else
                    array_push($deleted, $res['message']);
            }
            return response([
                'deleted' => $deleted,
                'errors' => $errors,
                'message' => 'Было удалено ролей: ' . count($deleted) . ' из ' . count($list)
            ]);
        }
    }

    public function delete($id)
    {
        $role = Role::find($id);
        if (empty($role))
            return ['error' => RolesExceptions::roleNotExists()->getMessage(), 'code' => 400];

        $roleName = $role->name;

        $role->delete();
        return ['success' => true, 'message' => 'Успешно удалено: роль ' . $roleName, 'error' => false];
    }

    /* если указать в $request->query('noError'), будет возвращен обычный ответ, без ошибки */
    public function checkPageAccess(Request $request)
    {
        $user = null;

        if (!User::authenticate($request)) {
            $noError = $request->query('noError');
            if (empty($noError || $noError === 'false'))
                return AuthExceptions::authFaildedResponse();

            return response(['success' => false, 'error' => true]);
        }

        $user = User::find($request->cookie('user'));

        $userRole = Role::find($user->role_id);
        if (empty($userRole))
            return response(['error' => RolesExceptions::roleNotExists()->getMessage()], 400);

        if ($userRole->priority === 1)
            return response(['success' => true, 'error' => false]);

        $userRoleName = $userRole->name;

        $keyExists = array_key_exists($userRoleName, Role::$allowedPages);
        $allowedPages = $keyExists
            ? Role::$allowedPages[$userRoleName] : [];
        if (is_numeric(array_search($request->query('page'), $allowedPages)))
            return response(['success' => true, 'error' => false]);
    }
}
