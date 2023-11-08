<?php

namespace App\Exceptions;

use Exception;

class OrdersExceptions extends Exception
{
    public static function emptyCartResponse()
    {
        return response(['error' => 'Корзина пуста'], 400);
    }
}
