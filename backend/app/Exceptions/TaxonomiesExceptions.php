<?php

namespace App\Exceptions;

use Exception;

class TaxonomiesExceptions extends Exception
{
    public static function notExists()
    {
        return new self('Таксономия не существует');
    }

    public static function storeValidator($title)
    {
        return [
            'name.required' => 'Не указано название для таксономии "' . $title . '"',
            'name.unique' => $title . ' с таким названием уже существует'
        ];
    }
}