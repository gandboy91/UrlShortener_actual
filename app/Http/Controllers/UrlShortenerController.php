<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\UrlHasher;
use App\Validators\UrlChecker;
use App\Services\ShortenerUrlManager;
use Illuminate\Http\Request;
use Validator;

/**
 * Class UrlShortenerController
 * @package App\Http\Controllers
 */
class UrlShortenerController extends Controller
{
    /**
     * returns main page or redirects to url using slug
     * @param Request $request 
     * @return \Illuminate\View\View|\Illuminate\Routing\Redirector 
     */
    public function main(Request $request)
    {
        if ($slug = $request->slug)
            return $this->redirectBySlug(''.$slug);
        return view('UrlShortener.main');
    }

    /**
     * Adding new url to DB and returns slug for it
     * @param ShortenerUrlManager $UrlManager 
     * @return string - responce in json
     */
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

    /**
     * returns redirector to url found by slug 
     * @param string $slug 
     * @return \Illuminate\Routing\Redirector
     */
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