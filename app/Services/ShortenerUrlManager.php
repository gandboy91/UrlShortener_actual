<?php

namespace App\Services;

use App\Contracts\UrlStorageManager;
use App\Models\UrlStorage;
use Illuminate\Http\Request;
use App\Validators\UrlChecker;
use Validator;

class ShortenerUrlManager implements UrlStorageManager 
{
    private $requestData;
    private $sanitizedUrl;
    private $allowedUrlLength = 5000;

    public function __construct(Request $request)
    {
        $this->requestData = $request;
    }

    public static function findUrlById($id)
    {
        $url = '';
        $urlRow = UrlStorage::find($id);
        if ($urlRow)
            $url = $urlRow->url;
        return $url;
    }
        
    public function findIdByUrl($url)
    {
        $urlId = 0;
        $urlRow = UrlStorage::where('url', '=', $url)->take(1);
        if ($urlRow->exists())
            $urlId = $urlRow->first()->id;
        return $urlId;
    }

    public function saveUrlAndReturnId($url)
    {
        $urlRow = UrlStorage::create(['url' => $url,'tstamp' => time()]);
        return $urlRow->id;
    }

    public function validateUrl()
    {
        // error codes
        // 200 OK
        // 400 Incorrect url 
        // 410 Not active url
        $statusCode = 200;
        $longUrl = $this->requestData->longUrl;
        $urlValidator = Validator::make(['longUrl'=>$longUrl], ['longUrl' => 'url|active_url']);
        if ($urlValidator->fails()) {
            $statusCode = 410;        
        } else {
            if (filter_var($longUrl, FILTER_VALIDATE_URL) 
            && UrlChecker::checkUrl($longUrl,$this->allowedUrlLength)) {
                $longUrl = filter_var($longUrl, FILTER_SANITIZE_URL); 
                if (mb_substr($longUrl,-1)=='/')
                    $longUrl = mb_substr($longUrl,0,-1); 
                $this->sanitizedUrl = $longUrl; 
            } else {
                $statusCode = 400;   
            }
        }
        return $statusCode; 
    }

    public function getSanitizedUrl()
    {
        return $this->sanitizedUrl; 
    }
}