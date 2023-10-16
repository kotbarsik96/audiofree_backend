<?php

namespace App\Exceptions;

use Exception;

class ProductsExceptions extends Exception
{
    protected static $storeAndUpdateMessages = [
        'required' => 'Не указано поле :attribute',
        'string' => 'Неверно указано поле :attribute',
        'numeric' => 'Неверно указано поле :attribute',
        'exists' => ':attribute не существует',
    ];

    public static function storeValidator($request)
    {
        return array_merge(self::$storeAndUpdateMessages, [
            'name.unique' => 'Товар с названием ' . $request->name . ' уже существует'
        ]);
    }

    public static function updateValidator($request)
    {
        return self::$storeAndUpdateMessages;
    }

    public static function noProduct()
    {
        return new self('Такого товара не существует');
    }
}