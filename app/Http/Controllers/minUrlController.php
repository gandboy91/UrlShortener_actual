<?php
namespace App\Http\Controllers;

//namespaces для фасадов и моделей eloquent
use Validator;
use App\Http\Controllers\Controller;
use App\UrlStorage;
use Illuminate\Http\Request;

class UrlShortenerController extends Controller
{
  //private $UrlHashLength = 8;
  //private $charsToGen = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  //макс длина url для парсинга.
  private $UrlAllowedLength = 5000;
  public function index()
  {
  }
  public function mainPage()
  {
	$hsr = new Hasher('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
	die($hsr->echoo());
	//шаблон главной страницы
	return view('UrlShortener.main');
  }
  public function addUrl(Request $request)
  { 
	//добавление url - обработчик ajax
	$Hash = '';
	$response = array();
	$LongUrl = $request->LongUrl;
	//$len = (int)$request->len;
	$response['success'] = false;
	$ErrorCode = 0;
	//определяем хост
	$host = $request->server()['HTTP_HOST'];
	//$duplHash = '';
	//задаем коды возможных ошибок
	$ErrorTypes = [ 'UrlNotActive'=>'указанный адрес не является активным url',
					'HashDuplicated'=>'Такой url уже есть в базе',
					'DbWriteError'=>'Не удалось записать url в БД',
					'UrlIncorrect'=>'Некорректный url'];
	//проверка url на существование
	$UrlValidator = Validator::make(['url'=>$LongUrl], ['LongUrl' => 'url|active_url']);
	if ($UrlValidator->fails()) {
		$ErrorCode = 'UrlNotActive';		
	}
	else{
			//проверка корректности url
			if (filter_var($LongUrl, FILTER_VALIDATE_URL) && $this->chkUrl($url)){
				$url = filter_var($url, FILTER_SANITIZE_URL);
				//обрезаем крайний слэш, чтобы не дублировать site.ru site.ru/ 
				if (mb_substr($LongUrl,-1)=='/')
					$LongUrl = mb_substr($LongUrl,0,-1);
				//проверяем наличие Hash для заданного url
				$checkDupl = UrlStorage::where('url', '=', $url)->take(1);
				if ($checkDupl->exists())
				{
					//если дублируется - возвращаем Hash для этого url
					$duplHash = $host.'/'.$checkDupl->first()->Hash;
					$errcode = 3;
				}
				 else if ($Hash = $this->UrlToDB($url,$len))
				{
					//вызвали функцию добавления нового Hash. если все ок - возвращаем новый Hash
					$response['success'] = 1;
					$response['Hash'] = $host.'/'.$Hash;
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
	//выдаем сообщение об ошибке, Hash при дублировании
	if ($errcode)
		$response['err'] = $errmsgs[$errcode];
	if ($errcode===3 && !empty($duplHash))
		$response['duplHash'] = $duplHash;
	//возвращаем ответ в json
    return response()->json( $response );
  }
  private function UrlToDB($url,$len)
  {
	//добавление нового url
	$i=0;
    $err=0;	
	
	do  {
		//генерация Hash пока не будет уникальным
		 ++$i;
		 $Hash = $this->genShUrl($len);
		 if ($i>$this->mxAttempts)
		 {$err=1; break;}
		} 	
	while (UrlStorage::where('Hash', '=', $Hash)->take(1)->exists());
	if($err===0){
		//транзакция
		UrlStorage::create(['url' => $url,'tstamp' => time(),'Hash' => $Hash]);
		//возвращаем сгенерированный Hash
	    return $Hash;
	}
	return false;
  }
  private function genShUrl($len = 8)
  {
	//функция генерации случайного Hash заданной длины из заданного набора символов
	$chars = $this->charsToGen;
	$Hash = '';
	$ch = strlen($chars);
	//выбираем случайный символ
	for ($i = 0; $i < $len; ++$i)
		$Hash .= substr($chars, (mt_rand() % $ch), 1);
	return $Hash;
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