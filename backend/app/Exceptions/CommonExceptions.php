<?php

namespace App\Exceptions;

use Exception;

class CommonExceptions extends Exception
{
    public static function incorrectDataResponse(){
        return response(['error' => 'Переданы некорректные данные'], 400);
    }
}