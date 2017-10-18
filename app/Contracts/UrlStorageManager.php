<?php

namespace App\Contracts;

interface UrlStorageManager
{
    public function findIdByUrl($url);
    
    public function saveUrlAndReturnId($url);

    public static function findUrlById($id);
}
