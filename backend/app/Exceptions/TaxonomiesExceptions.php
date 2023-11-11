<?php

namespace App\Exceptions;

use Exception;

class TaxonomiesExceptions extends Exception
{
    public static function notExists()
    {
        return new self('Таксономия не существует');
    }

    public static function alreadyExistsResponse($taxonomyTypeTitle, $name)
    {
        $error = $taxonomyTypeTitle . ' с названием ' . $name . ' уже существует';
        return response(['error' => $error], 400);
    }
}
