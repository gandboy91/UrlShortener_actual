<?php
namespace App\Http\Controllers;

use App\User;
use App\ShopProducts;
use App\Http\Controllers\Controller;

class testspeed extends Controller
{
 
  public function testspeed()
  {
	//  $time1 = microtime(true);
	
	$vanni = array();
    $products = ShopProducts::where('catalogue_id', 28)
               ->orderBy('id', 'desc')
               ->take(40)
               ->get();
	foreach ($products as $k=>$v)
	{
				  $vanni[$k]['id']= $v->id;
                  $vanni[$k]['name']= $v->name;
                  $vanni[$k]['cname']= $v->country->name;
                  $vanni[$k]['slug'] =  $v->slug;
                  $vanni[$k]['price'] =  $v->price;
				  $vanni[$k]['d'] =  $v->d;
				  $vanni[$k]['sh'] =  $v->sh;
				  $vanni[$k]['v'] =  $v->v;
				  if($sk = $v->sklads)
				  {
					  foreach ($sk as $m=>$s)
					  {
						 if($cnt = $s->count)
							{
							 $vanni[$k]['amount'][$m]['amount'] = $cnt;
							 $vanni[$k]['amount'][$m]['sklad'] = $s->sklad->name;
							}
					  }
				  }
				  
			  if($im = $v->images()->where('position', '1')->take(1)->get())
				 {
					  $vanni[$k]['imgname'] = $im[0]->file_name;
				 }
	}
    return view('Vtestspeed', ['vanni' => $vanni]);

  }
}