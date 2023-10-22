<?php 

namespace App\Exceptions;

use Exception;

class ImagesExceptions extends Exception {
    public static function noImage()
    {
        return new self('Изображение не найдено');
    }

    public static function uploadsLimitExceededResponse($limit = 0)
    {
        $message = 'Превышен лимит количества загружаемых изображений за раз';
        if($limit) 
            $message .= ' (' . $limit . ' шт.)';
        return response(['error' => $message], 400);
    }
}