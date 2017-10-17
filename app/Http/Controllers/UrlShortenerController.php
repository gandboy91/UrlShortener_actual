<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\UrlHasher;
use App\Services\ShortenerUrlManager;
use Illuminate\Http\Request;
use Validator;

class UrlShortenerController extends Controller
{
  private $AllowedUrlLength = 5000;
  private $ErrorTypes = [ 'UrlNotActive'=>'указанный адрес не является активным url',
						  'DbWriteError'=>'Не удалось записать url в БД',
						  'IncorrectUrl'=>'Некорректный url' ];

  public function main(Request $request)
  {
  	if($request->slug) {
  		$message = '';
		$slug = trim($request->slug);
		$validator = Validator::make(['slug'=>$slug], ['slug' => 'alpha_num']);
		if ($validator->fails()) {
			$message = 'Недопустимые символы в кратком url';		
		} else {
			$IdOfUrl = UrlHasher::HashToId($slug); 
			$LongUrl = ShortenerUrlManager::findUrlById($IdOfUrl);
			if ($LongUrl) {
				return redirect($LongUrl); 
			} else {
				$message = 'Не найдено соответствия для этого краткого url.';
			}
		}
		return redirect()->route('UrlShortener/main')->with('message',$message);
  	} else {
  		return view('UrlShortener.main');
  	}
  }

  public function addUrl(ShortenerUrlManager $UrlManager)
  {
  	$responce = array();
  	$shortUrl = $hash = $errorCode = '';
  	$ResponceStatus = 'ok';
	$ValidationStatus = $UrlManager->validate($this->AllowedUrlLength);
	if ($ValidationStatus === 'ok') {
		$SanitizedUrl = $UrlManager->getSanitizedUrl();
		$IdOfUrl = $UrlManager->findIdByUrl($SanitizedUrl);
		if ($IdOfUrl!==0) {
			$hash = UrlHasher::IdToHash($IdOfUrl);
		} else {
			if ($newUrlId = $UrlManager->saveUrl($SanitizedUrl))
				$hash = UrlHasher::IdToHash($newUrlId);
			else 
				$errorCode = 'DbWriteError';	
		}
		$shortUrl = url("$hash"); 
	} else {
		$errorCode = $ValidationStatus;
	}
	if (!empty($errorCode))
		$ResponceStatus = $this->ErrorTypes[$errorCode];
	$responce['status'] = $ResponceStatus;
	$responce['shortUrl'] = $shortUrl;
	return json_encode($responce);
  }
}