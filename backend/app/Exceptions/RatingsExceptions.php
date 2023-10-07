<?php 

namespace App\Exceptions;

use Exception;

class RatingsExceptions extends Exception {
    public static function storeValidator()
    {
        return [
            'required' => 'Не указано: :attribute',
            'value.numeric' => 'Значение рейтинга должно быть числом',
            'product_id.exists' => 'Товар не существует'
        ];
    }

    public static function noRating($productName)
    {
        return new self('Вы не ставили оценку товару ' . $productName);
    }
}