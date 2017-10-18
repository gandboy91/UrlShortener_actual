<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\UrlHasher;
use App\Validators\UrlChecker;
use App\Services\ShortenerUrlManager;
use Illuminate\Http\Request;
use Validator;

class UrlShortenerController extends Controller
{
    public function main(Request $request)
    {
        if ($slug = $request->slug)
            return $this->redirectBySlug($slug);
        return view('UrlShortener.main');
    }

    public function addUrl(ShortenerUrlManager $UrlManager)
    {
        $responce = array();
        $shortUrl = $hash = '';
        $responceStatus = 200;
        $validationStatus = $UrlManager->validateUrl();
        if ($validationStatus === 200) {
            $sanitizedUrl = $UrlManager->getSanitizedUrl();
            $idOfUrl = $UrlManager->findIdByUrl($sanitizedUrl);
            if ($idOfUrl!==0) {
                $hash = UrlHasher::IdToHash($idOfUrl);
            } else {
                if ($newUrlId = $UrlManager->SaveUrlAndReturnId($sanitizedUrl))
                    $hash = UrlHasher::idToHash($newUrlId);
                else 
                    $responceStatus = 500; // DB write error    
            }
            $shortUrl = url("$hash"); 
        } else {
            $responceStatus = $validationStatus;
        }
        $responce['status'] = $responceStatus;
        $responce['shortUrl'] = $shortUrl;
        return json_encode($responce);
    }

    private function redirectBySlug($slug)
    {
        $slug = trim($slug);
        $message = '';
        $statusCode = UrlChecker::checkSlug($slug);
        if ($statusCode === 200) {
            $idOfUrl = UrlHasher::hashToId($slug);
            if ($longUrl = ShortenerUrlManager::findUrlById($idOfUrl))
                return redirect($longUrl);
            else
                $statusCode = 410;
        }
        $message = UrlChecker::getErrorDescription($statusCode);
        return redirect()->route('UrlShortener/main')->with('message',$message);
    }
};	