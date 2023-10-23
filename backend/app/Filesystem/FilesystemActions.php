<?php

namespace App\Filesystem;

class FilesystemActions
{
    public static function strToPathAcceptable($subpath)
    {
        return strtolower(preg_replace('/\s/', '_', $subpath));
    }
}
