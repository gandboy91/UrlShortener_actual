<?php

namespace App\Validators;

use Validator;

/**
 * Class UrlChecker
 * @package app\Validators
 */
class UrlChecker
{
    /**
     * @var string regexp to filter url
     */
    private static $regexpForUrl = '/^(https?:\/\/)?([\w\.]+)\.([a-z]{2,6}\.?)(\/[\w\.,-?\[\]=&;#%]*)*\/?$/';  
    /**
     * @var array slug error list
     */
    private static $slugErrorDescription = [ 
        400 => 'Недопустимые символы в кратком url',
        410 => 'Не найдено соответствия для этого краткого url' ];
    /**
     * @var array url error list
     */
    private static $urlErrorDescription = [ 
        400 => 'Некорректный Url.',
        410 => 'Данный Url не активен.',
        411 => 'Введите Url.',
        500 => 'Ошибка записи в БД.' ];

    /**
     * extra checking url using regexp and max allowed length
     * @param string $url 
     * @param int $allowedLength 
     * @return bool 
     */
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

    /**
     * checking slug to suit a-z & 0-9 chars format
     * @param string $slug 
     * @return int - status code
     */
    public static function checkSlug($slug)
    {
        $statusCode = 200;
        $validator = Validator::make(['slug'=>$slug], ['slug' => 'alpha_num']);
        if ($validator->fails()) {
            $statusCode = 400;      
        }
        return $statusCode; 
    }

    /**
     * returns description of asked slug error code
     * @param int $errorCode 
     * @return string 
     */
    public static function getSlugErrorDescription($errorCode)
    {
        return self::$slugErrorDescription[$errorCode]; 
    }
    
    public static function getUrlErrorDescription($errorCode)
    {
        return self::$urlErrorDescription[$errorCode]; 
    }    
}