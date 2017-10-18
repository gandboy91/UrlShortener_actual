<?php

namespace App\Validators;

use Validator;

class UrlChecker
{
    private static $regexpForUrl = '/^(https?:\/\/)?([\w\.]+)\.([a-z]{2,6}\.?)(\/[\w\.,-?\[\]=&;#%]*)*\/?$/';  
    private static $errorDescription = [ 
        400 => 'Недопустимые символы в кратком url',
        410 => 'Не найдено соответствия для этого краткого url' ];

    public static function checkUrl($url, $allowedLength)
    {
        if (strpos($url, ' ') > -1)
            return false;
        if(strlen($url) > $allowedLength)
            return false;
        if(!preg_match(self::$regexpForUrl, $url))
            return false;
        if (strpos($url, '.') > 1) { 
            $parsedUrl = @parse_url($url);
            return is_array($parsedUrl)
            && isset($parsedUrl['scheme'])
            && isset($parsedUrl['host']) 
            && count(explode('.', $parsedUrl['host'])) > 1;
        }
        return true;
    }

    public static function checkSlug($slug)
    {
        $statusCode = 200;
        $validator = Validator::make(['slug'=>$slug], ['slug' => 'alpha_num']);
        if ($validator->fails()) {
            $statusCode = 400;      
        }
        return $statusCode; 
    }

    public static function getErrorDescription($errorCode)
    {
        return self::$errorDescription[$errorCode]; 
    }    
}