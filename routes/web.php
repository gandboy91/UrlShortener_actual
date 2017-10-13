<?php

// URL-shortener  begin
Route::get('/', ['as' => 'minUrl/main', 'uses' =>'minUrlController@minUrl']);
Route::get('mytest','minUrlController@mytest');
Route::post( 'minUrl/new','minUrlController@addUrl');
Route::get('/{slug}', function ($slug = null) {
	if($slug){
		$slug = trim($slug);
		$msg = '';
		$res = App\UrlStorage::where('slug', '=', $slug)->take(1);
		if ($res->exists())
		    {
				$validator = Validator::make(['slug'=>$slug], ['slug' => 'alpha_num']);
				if ($validator->fails()) {
					$msg = 'Недопустимые символы в кратком url';		
				} else {
					$finUrl = $res->first()->url;
					return redirect($finUrl); 
				}
			}
			else
			{
				$msg = 'Не найдено соответствия для этого краткого url.';
			}
		}
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
