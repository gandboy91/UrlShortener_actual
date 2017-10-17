<?php

// URL-shortener  begin
Route::get('/', ['as' => 'UrlShortener/main', 'uses' =>'UrlShortenerController@main']);
Route::post('minUrl/new','UrlShortenerController@addUrl');
Route::get('/{slug}', 'UrlShortenerController@main');

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
