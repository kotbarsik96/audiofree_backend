<?php 

namespace App\Exceptions;

use Exception;

class ImagesExceptions extends Exception {
    public static function noImage()
    {
        return new self('Изображение не найдено');
    }
}