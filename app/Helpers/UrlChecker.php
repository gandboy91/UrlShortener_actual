<?php

namespace app\Helpers;

class UrlChecker
{
    public static function CheckUrl($url, $allowedLength)
    {
        if (strpos($url, ' ') > -1)
            return false;
        if(strlen($url) > $allowedLength)
            return false;
        if (strpos($url, '.') > 1) {
            $ParsedUrl = @parse_url($url);
            return is_array($ParsedUrl)
            && isset($ParsedUrl['scheme'])
            && isset($ParsedUrl['host']) 
            && count(explode('.', $ParsedUrl['host'])) > 1;
        }
    return true;
    }
}