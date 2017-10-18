<?php

namespace app\Helpers;

class UrlHasher
{
    //base58
    private static $baseChars = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

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

    public static function hashToId($hash)
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