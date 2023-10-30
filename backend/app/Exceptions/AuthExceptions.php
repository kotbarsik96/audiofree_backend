<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\JsonResponse;

class AuthExceptions extends Exception
{
    protected static $passwordMinMsg = 'Пароль должен содержать не менее :min символов';
    public static $emailFormat = 'Формат электронной почты: example@mail.com';
    public static $phoneFormat = 'Формат номера телефона: +7 (999) 999 - 99 - 99';

    public static function registerValidator()
    {
        return [
            'name.required' => 'Не указано имя',
            'email.required' => 'Не указан email',
            'email.email' => self::$emailFormat,
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'password.required' => 'Не указан пароль',
            'password.min' => self::$passwordMinMsg,
            'password.string' => 'Не указан пароль',
            'password_confirmation.required' => 'Необходимо подтвердить пароль',
            'password_confirmation.same' => 'Пароли не совпадают'
        ];
    }

    public static function registerError()
    {
        return new self('Произошла ошибка при регистрации');
    }

    public static function loginValidator()
    {
        return [
            'email.required' => 'Не указан email',
            'password.required' => 'Не указан пароль'
        ];
    }

    public static function loginIncorrectData()
    {
        return new self('Неверные данные для входа');
    }

    public static function authFaildedResponse()
    {
        return response(['error' => 'Провал проверки авторизации'], 403);
    }

    public static function changePasswordValidator()
    {
        return [
            'password.required' => 'Не указан текущий пароль',
            'new_password.required' => 'Не указан новый пароль',
            'new_password.min' => self::$passwordMinMsg,
            'new_password_confirmation.same' => 'Новый пароль подтвержден неверно',
        ];
    }

    public static function changePasswordSame()
    {
        return new self('Пароли совпадают, изменение не произошло');
    }

    public static function incorrectPassword()
    {
        return new self('Неверно введен пароль');
    }

    public static function userNotExists()
    {
        return new self('Пользователь не существует');
    }

    public static function userNotLoggedIn()
    {
        return new self('Пользователь не авторизован');
    }

    public static function alreadyLoggedIn()
    {
        return new self('Вы уже авторизованы');
    }

    public static function incorrectVerifyingEmailCode()
    {
        return new self('Неверный код подтверждения email');
    }

    public static function emailAlreadyVerified()
    {
        return new self('Email уже подтвержден');
    }
}