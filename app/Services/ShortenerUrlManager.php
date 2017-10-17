<?php

namespace App\Services;

use App\Contracts\UrlStorageManager;
use App\Models\UrlStorage;
use Illuminate\Http\Request;
use App\Helpers\UrlChecker;
use Validator;

class ShortenerUrlManager implements UrlStorageManager 
{
    private $requestData;
    private $SanitizedUrl;

    public function __construct(Request $request)
    {
        $this->requestData = $request;
    }

    public static function findUrlById($id)
    {
        $Url = '';
        $UrlRow = UrlStorage::find($id);
        if ($UrlRow)
            $Url = $UrlRow->url;
        return $Url;
    }
        
    public function findIdByUrl($url)
    {
        $UrlId = 0;
        $UrlRow = UrlStorage::where('url', '=', $url)->take(1);
        if ($UrlRow->exists())
            $UrlId = $UrlRow->first()->id;
        return $UrlId;
    }

    public function saveUrl($url)
    {
        $UrlRow = UrlStorage::create(['url' => $url,'tstamp' => time()]);
        return $UrlRow->id;
    }

    public function validate($allowedUrlLength)
    {
        $StatusCode = '';
        $LongUrl = $this->requestData->LongUrl;
        $UrlValidator = Validator::make(['LongUrl'=>$LongUrl], ['LongUrl' => 'url|active_url']);
        if ($UrlValidator->fails()) {
            $StatusCode = 'UrlNotActive';        
        } else {
                //checking if url is correct
                if (filter_var($LongUrl, FILTER_VALIDATE_URL) 
                && UrlChecker::CheckUrl($LongUrl,$allowedUrlLength)) {
                    $LongUrl = filter_var($LongUrl, FILTER_SANITIZE_URL); 
                    if (mb_substr($LongUrl,-1)=='/')
                    $LongUrl = mb_substr($LongUrl,0,-1);
                    $StatusCode = 'ok'; 
                    $this->SanitizedUrl = $LongUrl; 
                } else {
                    $StatusCode = 'IncorrectUrl';   
                }
        }
        return $StatusCode; 
    }

    public function getSanitizedUrl()
    {
        return $this->SanitizedUrl; 
    }
}