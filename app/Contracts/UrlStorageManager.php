<?php

namespace App\Contracts;

interface UrlStorageManager
{
    public function findIdByUrl($url);

    public static function findUrlById($id);
    
    public function saveUrl($url);
}
