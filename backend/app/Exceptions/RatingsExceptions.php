<?php 

namespace App\Exceptions;

use Exception;

class RatingsExceptions extends Exception {
    public static function noRating($productName)
    {
        return new self('Вы не ставили оценку товару ' . $productName);
    }
}