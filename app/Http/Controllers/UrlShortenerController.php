<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\UrlHasher;
use App\Validators\UrlChecker;
use App\Services\ShortenerUrlManager;
use Illuminate\Http\Request;
use Response;
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
     * @param ShortenerUrlManager $urlManager 
     * @return string - response in json
     */
    public function addUrl(ShortenerUrlManager $urlManager)
    {
        $response = array();
        $shortUrl = $hash = '';
        $responseStatus = $urlManager->validateUrl();
        if ($responseStatus === 200) {
            $sanitizedUrl = $urlManager->getSanitizedUrl();
            $idOfUrl = $urlManager->findIdByUrl($sanitizedUrl);
            if ($idOfUrl!==0) {
                $hash = UrlHasher::IdToHash($idOfUrl);
            } else {
                if ($newUrlId = $urlManager->saveUrlAndReturnId($sanitizedUrl))
                    $hash = UrlHasher::idToHash($newUrlId);
                else 
                    $responseStatus = 500; // DB write error    
            }
            $shortUrl = url("$hash");
            $response['shortUrl'] = $shortUrl; 
        } else {
            $response['error'] = UrlChecker::getUrlErrorDescription($responseStatus);
        }
        return Response::json($response,$responseStatus);
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
        $message = UrlChecker::getSlugErrorDescription($statusCode);
        return redirect()->route('UrlShortener/main')->with('message',$message);
    }
};	