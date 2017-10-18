<?php

namespace app\Helpers;

/**
 * Class UrlHasher
 * @package app\Helpers
 */
class UrlHasher
{
    /** 
     * @var string - chars used to encode in base_58
     */
    private static $baseChars = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * makes hash from id
     * @param integer $int
     * @return string
     */
    public static function idToHash($int)
    {
        $hash = "";
        $int = intval($int);
        $base = strlen(self::$baseChars);
        while($int >= $base) {
            $div = intval(floor($int / $base));
            $mod = intval($int - ($base * $div));
            $hash = self::$baseChars{$mod} . $hash;
            $int = $div;
        }
        if($int) 
            $hash = self::$baseChars{intval($int)} . $hash;
        return $hash; 
    }

    /**
     * makes id from hash 
     * @param string $hash 
     * @return int
     */
    public static function hashToId($hash)
    {
        $int = 0;
        $base=strlen(self::$baseChars);
        $lastCharPosition = strlen($hash)-1;
        for($i = $lastCharPosition, $j = 1; $i >= 0; $i--, $j *= $base) {
            $int += $j * strpos(self::$baseChars, $hash{$i});
        }
        return intval($int);   
    }
}