<?php

namespace App\Exceptions;

use Exception;

class UsersExceptions extends Exception
{
    public static function updateRoleValidator()
    {
        return [
            'id.exists' => 'Пользователя с таким id не существует',
            'id.numeric' => 'id должен быть числовым',
            'email.exists' => 'Пользователя с таким email не существует',
            'email.email' => 'Пользователя с таким email не существует'
        ];
    }

    public static function roleUpdateFail()
    {
        return new self('Невозможно задать данную роль');
    }
}