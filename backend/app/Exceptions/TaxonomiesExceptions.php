<?php

namespace App\Exceptions;

use Exception;

class TaxonomiesExceptions extends Exception
{
    public static function notExists()
    {
        return new self('Таксономия не существует');
    }
}