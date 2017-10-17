<?php

namespace app\Helpers;

class UrlHasher
{
    private static $hasher = null;
    private static $baseChars = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    public static function IdToHash($int)
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

    public static function HashToId($hash)
    {
        $int = 0;
        $base=strlen(self::$baseChars);
        $lastCharPosition = strlen($hash)-1;
        for($i = $lastCharPosition, $j = 1; $i >= 0; $i--, $j *= $base) {
            $int += $j * strpos(self::$baseChars, $hash{$i});
        }
        return $int;   
    }
}