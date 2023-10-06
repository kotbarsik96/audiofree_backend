<?php

namespace App\Exceptions;

use Exception;

class UserEntitiesExceptions extends Exception
{
    public static function storeUserEntitiesValidator()
    {
        return [
            'user_id.required' => 'Не указан id пользователя',
            'user_id.exists' => 'Пользователь с таким id не существует'
        ];
    }
}