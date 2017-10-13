<?php

// URL-shortener  begin

// запрос на главную страницу
Route::get('/', ['as' => 'minUrl/main', 'uses' =>'minUrlController@minUrl']);
// ajax запрос на добавление url и генерацию slug
Route::post( 'minUrl/new','minUrlController@addUrl');
// запрос страницы по slug
Route::get('/{slug}', function ($slug = null) {
	if($slug){
		$slug = trim($slug);
		$msg = '';
		//проверяем slug - принимаем только латиницу + цифры
		$validator = Validator::make(['slug'=>$slug], ['slug' => 'alpha_num']);
		if ($validator->fails()) {
			$msg = 'Недопустимые символы в кратком url';		
		}
			else
		{
			//если проверка по символам прошла выбираем из БД нужный url
			$res = App\UrlStorage::where('slug', '=', $slug)->take(1);
			if ($res->exists())
		    {
				$finUrl = $res->first()->url;
				//перенаправляем
				return redirect($finUrl); 
			}
			else
			{
				$msg = 'Не найдено соответствия для этого краткого url.';
			}
		}
		
	}
	//возвращаем на главную с сообщением об ошибке
	return redirect()->route('minUrl/main')->with('msg',$msg);
});
// URL-shortener  end
//stc
Route::get('testspeed', 'testspeed@testspeed');
Route::get('stc/', 'stcController@index');

Route::get('stc/AddEl', 'stcController@AddElement');
Route::post('PostAddEl', 'stcController@PostAddEl');
Route::post('PostUpdEl', 'stcController@PostUpdEl');

Route::get('stc/element/{id}', ['as' => 'element_view', 'uses' =>'stcController@ElManage']);
Route::get('stc/delEl/{id}', function ($id = null) {
	if($id){
	$dep = App\Elements::find($id)->dependencies;
	$dep = json_decode($dep, true);
	foreach($dep as $k=>$v){
		if ($v!='b')
		{	$el = App\Elements::find($k);
			switch($v)
			{
				case 'n': $el->ifUsed = $el->ifUsed == 'b' ? 'y' : ''; break;
				case 'y': $el->ifUsed = $el->ifUsed == 'b' ? 'n' : ''; break;
			}
			$el->save();			
		}
	}
	App\Elements::destroy($id);
	return redirect()->back()->with('msg', 'Элемент был успешно удален.');	
	}
});
