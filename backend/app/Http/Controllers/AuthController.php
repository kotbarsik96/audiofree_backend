<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\AuthExceptions;
use App\Models\User;
use App\Models\UserEntities\Cart;
use App\Models\UserEntities\Favorite;
use App\Http\Controllers\UserEntitiesController;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use App\Models\VerifyEmail;

function generate_pass()
{
    $chars = '-#$!%^&*;.qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';
    $chars_split = mb_str_split($chars);
    $count = count($chars_split);
    $length = random_int(15, 20);

    $pass = '';
    for ($i = 0; $i < $length; $i++) {
        $j = random_int(0, $count - 1);
        $char = $chars_split[$j];
        $pass .= $char;
    }
    while (!preg_match('/[-#$!%^&*;.]/', $pass)) {
        $pass = generate_pass();
    }
    return $pass;
}

class AuthController extends Controller
{
    public $roleDefault = 3;
    protected $passwordRequirement = 'string|min:7|required';
    protected $userCookiesMinutes = 60 * 240;

    public function registerValidationReq(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email|email:dns',
            'password' => $this->passwordRequirement,
            'password_confirmation' => 'same:password'
        ], AuthExceptions::registerValidator());
    }

    public function changePasswordValidationReq(Request $request)
    {
        return Validator::make($request->all(), [
            'password' => 'required|string',
            'new_password' => $this->passwordRequirement,
            'new_password_confirmation' => 'same:new_password'
        ], AuthExceptions::changePasswordValidator());
    }

    public function register(Request $request)
    {
        $checkAuth = $this->checkAuth($request);
        if (is_array($checkAuth))
            return response(['error' => AuthExceptions::alreadyLoggedIn()->getMessage()], 400);

        $validator = $this->registerValidationReq($request);

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 422);


        $role = Role::find($this->roleDefault);
        if (empty($role))
            return response(['error' => AuthExceptions::registerError()->getMessage()], 422);

        UserEntitiesController::clearUserEntities();
        $data = array_merge(
            $validator->validated(),
            ['role_id' => $role->id]
        );
        $user = User::create($data);
        Cart::create(['user_id' => $user->id]);
        Favorite::create(['user_id' => $user->id]);

        return response()
            ->json([
                'success' => true,
                'message' => 'Здравствуйте, ' . $user->name . '! Регистрация прошла успешно',
                'user_id' => $user->id
            ])
            ->cookie('user', $user->id, $this->userCookiesMinutes, null, env('SESSION_DOMAIN'))
            ->cookie('userAdd', bcrypt($user->email . $user->id), $this->userCookiesMinutes, null, env('SESSION_DOMAIN'));
    }

    public function login(Request $request)
    {
        $checkAuth = $this->checkAuth($request);
        if (is_array($checkAuth))
            return response(['error' => AuthExceptions::alreadyLoggedIn()->getMessage()], 422);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], AuthExceptions::loginValidator());

        if ($validator->fails())
            return response(['errors' => $validator->errors()], 422);

        $credentials = $validator->validated();
        $user = User::where('email', $credentials['email'])->first();
        if (!$user)
            return response(
                ['error' => AuthExceptions::loginIncorrectData()->getMessage()],
                422
            );

        if (!Hash::check($credentials['password'], $user->password))
            return response(
                ['error' => AuthExceptions::loginIncorrectData()->getMessage()],
                422
            );

        if (!Auth::attempt($credentials))
            return response(
                ['error' => AuthExceptions::loginIncorrectData()->getMessage()],
                422
            );

        return response()
            ->json([
                'success' => true,
                'message' => 'Здравствуйте, ' . $user->name . '!',
                'user_id' => $user->id
            ])
            ->cookie('user', $user->id, $this->userCookiesMinutes, null, env('SESSION_DOMAIN'))
            ->cookie('userAdd', bcrypt($user->email . $user->id), $this->userCookiesMinutes, null, env('SESSION_DOMAIN'));
    }

    public function logout(Request $request)
    {
        $userId = $request->cookie('user');
        $user = User::find($userId);

        if (empty($user))
            return AuthExceptions::checkAuthFailed();

        Auth::logout();

        return response()
            ->json([
                'success' => true,
                'message' => 'До свидания, ' . $user->name
            ])
            ->cookie(Cookie::forget('user'))
            ->cookie(Cookie::forget('userAdd'));
    }

    // метод сначала проходит auth:sanctum. Таким образом, если до выполнения метода дело не дошло, значит пользователь не авторизован. В случае успеха возвращает array, в случае ошибки - JsonResponse
    public function checkAuth(Request $request, $userOnSuccess = false)
    {
        $userId = $request->cookie('user');
        $userSecret = $request->cookie('userAdd');
        if (empty($userId) || empty($userSecret))
            return AuthExceptions::checkAuthFailed();

        $user = User::find($userId);
        if (!$user || !Hash::check($user->email . $user->id, $userSecret))
            return AuthExceptions::checkAuthFailed();

        if ($userOnSuccess)
            return ['user' => $user, 'error' => false];
        else
            return ['success' => true, 'user_id' => $user->id, 'error' => false];
    }

    public function changePassword(Request $request)
    {
        $check = $this->checkAuth($request, true);
        if (!is_array($check))
            return $check;

        $user = $check['user'];

        $validator = $this->changePasswordValidationReq($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 422);

        $credentials = $validator->validated();
        if (!Hash::check($credentials['password'], $user->password))
            return response(['error' => AuthExceptions::incorrectPassword()->getMessage()], 422);

        $newPassword = $request->new_password;
        if ($newPassword === $credentials['password'])
            return response(['error' => AuthExceptions::changePasswordSame()->getMessage()], 422);

        $user->update([
            'password' => $newPassword
        ]);

        return response([
            'success' => true,
            'message' => 'Пароль успешно изменен'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $check = $this->checkAuth($request, true);
        if (!is_array($check))
            return $check;

        $user = $check['user'];
        $newPassword = generate_pass();
        $user->update([
            'password' => $newPassword
        ]);

        Mail::to($user->email)
            ->send(new ResetPassword(['user' => $user, 'newPassword' => $newPassword]));
    }

    // если нужно проверять право не из request, нужно обращаться напрямую к (new User())->checkUserRight($action)
    public static function checkUserRight(Request $request, $action)
    {
        $authController = new self();
        $checkOrUser = $authController->checkAuth($request, true);
        if (!is_array($checkOrUser)) {
            return [
                'has_right' => false,
                'error' => AuthExceptions::userNotLoggedIn()->getMessage()
            ];
        }

        return $checkOrUser['user']->checkUserRight($action);
    }

    public function getVerificationHash($email)
    {
        return 'verification_of_' . $email . '_email_user';
    }

    public function sendEmailVerification(Request $request)
    {
        $userId = $request->cookie('user');
        $user = User::find($userId);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotExists()->getMessage()], 422);

        if (!empty($user->email_verified_at))
            return response(['error' => AuthExceptions::emailAlreadyVerified()->getMessage()], 400);

        $codeFirst = random_int(100, 999);
        $codeSecond = random_int(100, 999);
        $code = (string) $codeFirst . (string) $codeSecond;

        $verifyModelData = [
            'user_id' => $userId,
            'code' => $code
        ];
        $verifyingCodeModel = VerifyEmail::where('user_id', $userId)->first();
        if ($verifyingCodeModel) {
            // отправлять запросы можно не чаще раза в минуту
            $passedSeconds = time() - strtotime($verifyingCodeModel->updated_at);
            if ($passedSeconds <= 60)
                return response(['error' => 'Повторное отправление возможно не чаще раза в минуту'], 400);

            $verifyingCodeModel->update($verifyModelData);
        } else {
            VerifyEmail::create($verifyModelData);
        }

        Mail::to($user->email)
            ->send(new EmailVerification(['user' => $user, 'code' => $code]));

        return response([
            'success' => true,
            'message' => 'Письмо с кодом подтверждения было отправлено на вашу почту'
        ]);
    }

    public function verifyEmail(Request $request, $code)
    {
        $userId = $request->cookie('user');
        $user = User::find($userId);
        if (empty($user))
            return response(['error' => AuthExceptions::userNotExists()->getMessage()], 400);

        $verifyingCodeModel = VerifyEmail::where('user_id', $userId)->first();
        if (empty($verifyingCodeModel))
            return response(['error' => AuthExceptions::incorrectVerifyingEmailCode()->getMessage()], 422);

        $verifyingCode = $verifyingCodeModel->code;
        if ((int) $verifyingCode !== (int) $code)
            return response(['error' => AuthExceptions::incorrectVerifyingEmailCode()->getMessage()], 422);

        $user->update([
            'email_verified_at' => date('Y-m-d H:i:s', time())
        ]);

        $allUserCodes = VerifyEmail::where('user_id', $userId)
            ->get();
        foreach ($allUserCodes as $userCodeModel) {
            $userCodeModel->delete();
        }

        return response([
            'success' => true,
            'message' => 'Ваш Email подтвержден'
        ]);
    }
}