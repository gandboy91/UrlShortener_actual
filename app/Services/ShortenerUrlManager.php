<?php

namespace App\Services;

use App\Contracts\UrlStorageManager;
use App\Models\UrlStorage;
use Illuminate\Http\Request;
use App\Validators\UrlChecker;
use Validator;

/**
 * Class UrlStorage
 * @package App\Models
 * @implements UrlStorageManager
 */
class ShortenerUrlManager implements UrlStorageManager 
{   
    /**
     * @var Request - data from ajax request  
     */
    private $requestData;
    private $sanitizedUrl;
    private $allowedUrlLength = 5000;

    /**
     * saves request data
     * @param Request $request 
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->requestData = $request;
    }

    /**
     * returns url finded by id
     * @param int $id 
     * @return string
     */
    public static function findUrlById($id)
    {
        $url = '';
        $urlRow = UrlStorage::find($id);
        if ($urlRow)
            $url = $urlRow->url;
        return $url;
    }
    
    /**
     * returns id finded by url
     * @param string $url 
     * @return int
     */    
    public function findIdByUrl($url)
    {
        $urlId = 0;
        $urlRow = UrlStorage::where('url', '=', $url)->take(1);
        if ($urlRow->exists())
            $urlId = $urlRow->first()->id;
        return $urlId;
    }

    /**
     * saves new url and returns it's id
     * @param string $url 
     * @return int
     */
    public function saveUrlAndReturnId($url)
    {
        $urlRow = UrlStorage::create(['url' => $url,'tstamp' => time()]);
        return $urlRow->id;
    }

    /**
     * validates long url from ajax request
     * 
     * status codes
     * 200 OK
     * 400 Incorrect url 
     * 410 Not active url
     *      
     * @return int - status code
     */
    public function validateUrl()
    {
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

    /**
     * return sanitized url
     * @return string
     */
    public function getSanitizedUrl()
    {
        return $this->sanitizedUrl; 
    }
}