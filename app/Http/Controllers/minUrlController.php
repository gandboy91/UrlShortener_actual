<?php
namespace App\Http\Controllers;

//namespaces для фасадов и моделей eloquent
use Validator;
use App\Http\Controllers\Controller;
use App\UrlStorage;
use Illuminate\Http\Request;

class minUrlController extends Controller
{
  //допустимые значения длины короткого url (slug)
  private $mnLen = 3;
  private $mxLen = 12;
  //макс количество попыток при генерации slug
  private $mxAttempts = 20;	
  //набор символов для генерации slug, макс длина url для парсинга.
  private $charsToGen = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  private $urlParseLen = 5000;
  public function index()
  {
  }
  public function minUrl()
  {
	//шаблон главной страницы
	return view('minUrl.main');
  }
  public function addUrl(Request $request)
  { 
	//добавление url - обработчик ajax
	$slug = '';
	$response = array();
	$url = $request->lUrl;
	$len = (int)$request->len;
	$response['success'] = 0;
	$errcode = 0;
	//определяем хост
	$host = $request->server()['HTTP_HOST'];
	$duplSlug = '';
	//задаем коды возможных ошибок
	$errmsgs = [1=>'указанный адрес не является активным url',
				2=>'Некорректная длина short-url',
				3=>'Такой url уже есть в базе',
				4=>'Не удалось записать url в БД',
				5=>'Некорректный url'];
	//проверка url на существование
	$validator = Validator::make(['url'=>$url], ['url' => 'url|active_url']);
	if ($validator->fails()) {
		$errcode = 1;		
	}
	else{
		if (!$len || $len < $this->mnLen || $len > $this->mxLen)
		{
			//проверка желаемой длины slug 
			$errcode = 2;
		}else{
			//проверка корректности url
			if (filter_var($url, FILTER_VALIDATE_URL) && $this->chkUrl($url)){
				$url = filter_var($url, FILTER_SANITIZE_URL);
				//обрезаем крайний слэш, чтобы не дублировать site.ru site.ru/ 
				if (mb_substr($url,-1)=='/')
					$url = mb_substr($url,0,-1);
				//проверяем наличие slug для заданного url
				$checkDupl = UrlStorage::where('url', '=', $url)->take(1);
				if ($checkDupl->exists())
				{
					//если дублируется - возвращаем slug для этого url
					$duplSlug = $host.'/'.$checkDupl->first()->slug;
					$errcode = 3;
				}
				 else if ($slug = $this->UrlToDB($url,$len))
				{
					//вызвали функцию добавления нового slug. если все ок - возвращаем новый slug
					$response['success'] = 1;
					$response['slug'] = $host.'/'.$slug;
				} else
				{   //при ошибке записи в БД
					$errcode = 4;
				}
			}
			else{
				//некорректном url
				$errcode = 5;	
			}
		}
	}
	//выдаем сообщение об ошибке, slug при дублировании
	if ($errcode)
		$response['err'] = $errmsgs[$errcode];
	if ($errcode===3 && !empty($duplSlug))
		$response['duplSlug'] = $duplSlug;
	//возвращаем ответ в json
    return response()->json( $response );
  }
  private function UrlToDB($url,$len)
  {
	//добавление нового url
	$i=0;
    $err=0;	
	
	do  {
		//генерация slug пока не будет уникальным
		 ++$i;
		 $slug = $this->genShUrl($len);
		 if ($i>$this->mxAttempts)
		 {$err=1; break;}
		} 	
	while (UrlStorage::where('slug', '=', $slug)->take(1)->exists());
	if($err===0){
		//транзакция
		UrlStorage::create(['url' => $url,'tstamp' => time(),'slug' => $slug]);
		//возвращаем сгенерированный slug
	    return $slug;
	}
	return false;
  }
  private function genShUrl($len = 8)
  {
	//функция генерации случайного slug заданной длины из заданного набора символов
	$chars = $this->charsToGen;
	$slug = '';
	$ch = strlen($chars);
	//выбираем случайный символ
	for ($i = 0; $i < $len; ++$i)
		$slug .= substr($chars, (mt_rand() % $ch), 1);
	return $slug;
  }
  private function chkUrl($url)
  { //проверка корректности url
	if (strpos($url, ' ') > -1)
        return false;
	if(strlen($url) > $this->urlParseLen)
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