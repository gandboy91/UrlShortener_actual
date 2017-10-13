<?php
namespace App\Http\Controllers;

use Validator;
use App\Http\Controllers\Controller;
use App\UrlStorage;
use Illuminate\Http\Request;

class minUrlController extends Controller
{
  private $mnLen = 3;
  private $mxLen = 12;
  private $mxAttempts = 20;	
  public function index()
  {
  }
  public function minUrl()
  {
	return view('minUrl.main');
  }
  public function addUrl(Request $request)
  { 
	$slug = '';
	$response = array();
	$url = $request->lUrl;
	$len = (int)$request->len;
	$response['success'] = 0;
	$errcode = 0;
	$host = $request->server()['HTTP_HOST'];
	$duplSlug = '';
	$errmsgs = [1=>'указанный адрес не является активным url',
				2=>'Некорректная длина short-url',
				3=>'Такой url уже есть в базе',
				4=>'Не удалось записать url в БД',
				5=>'Некорректный url'];
	$validator = Validator::make(['url'=>$url], ['url' => 'url|active_url']);
	if ($validator->fails()) {
		$errcode = 1;		
	}
	else{
		if (!$len || $len < $this->mnLen || $len > $this->mxLen)
		{
			$errcode = 2;
		}else{
			if (filter_var($url, FILTER_VALIDATE_URL) && $this->chkUrl($url)){
				$url = filter_var($url, FILTER_SANITIZE_URL);
				if (mb_substr($url,-1)=='/')
					$url = mb_substr($url,0,-1);
				$checkDupl = UrlStorage::where('url', '=', $url)->take(1);
				if ($checkDupl->exists())
				{
					$duplSlug = $host.'/'.$checkDupl->first()->slug;
					$errcode = 3;
				}
				 else if ($slug = $this->UrlToDB($url,$len))
				{
					$response['success'] = 1;
					$response['slug'] = $host.'/'.$slug;
				} else
				{
					$errcode = 4;
				}
			}
			else{
				$errcode = 5;	
			}
		}
	}
	if ($errcode)
		$response['err'] = $errmsgs[$errcode];
	if ($errcode===3 && !empty($duplSlug))
		$response['duplSlug'] = $duplSlug;
    return response()->json( $response );
  }
  private function UrlToDB($url,$len)
  {
	$i=0;
    $err=0;	
	
	do  {
		 ++$i;
		 $slug = $this->genShUrl($len);
		 if ($i>$this->mxAttempts)
		 {$err=1; break;}
		} 	
	while (UrlStorage::where('slug', '=', $slug)->take(1)->exists());
	if($err===0){
		UrlStorage::create(['url' => $url,'tstamp' => time(),'slug' => $slug]);
	    return $slug;
	}
	return false;
  }
  private function genShUrl($len = 8)
  {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$slug = '';
	$ch = strlen($chars);
	for ($i = 0; $i < $len; ++$i)
		$slug .= substr($chars, (mt_rand() % $ch), 1);
	return $slug;
  }
  private function chkUrl($url)
  {
	if (strpos($url, ' ') > -1)
        return false;
	if(strlen($url) > 5000)
		return false;
     if (strpos($url, '.') > 1) {
        $check = @parse_url($url);
        return is_array($check)
            && isset($check['scheme'])
            && isset($check['host']) 
			&& count(explode('.', $check['host'])) > 1;
	}
    return false;
  }
}