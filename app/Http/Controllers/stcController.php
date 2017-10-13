<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\ElTypes; //модель типов
use App\Elements; //модель элементов
use App\myTest;
use Illuminate\Http\Request;

class stcController extends Controller
{
  public function index()
  {
	  //получаем все элементы
	  $elements = array(); 
	  $elements = $this->GetElements(false);
	  return view('stc/main',['elements'=>$elements]);
  }
  public function minUrl()
  {
	return view('minUrl.main');
  }
  public function addUrl(Request $request)
  {
	 $response = array(
            'status' => 'ЗБС',
            'msg' => 'ok',
        );
        return response()->json( $response );
  }
  public function mytest()
  {
	echo "<pre>";
	$ur = parse_url('alphatest.sibway.pro/mytest');  
	var_dump($ur);
	$i=0;
	do 	{
		 ++$i;
		 $key = $this->getKey();
		 if ($i>20)
		 {echo "закончились варики";
			 break;
		 }
		} 	
	while (myTest::where('test', '=', $key)->take(1)->exists());
	myTest::create(['test' => $key]);
	
  }
  public function AddElement()
  { //метод добавления элемента
	//получаем все типы элементов для указания связей
	$types = $elements = array(); 
	$typesDB = ElTypes::get();	
	foreach($typesDB as $k=>$v){
		$types[$v->id]=$v->name;
	}
	//получаем свободный id
	$maxId = Elements::max('id')+1;
	$default = array('tId'=>2,'name'=>'','NewId'=>$maxId,'id'=>'','dep'=>'','action'=>'stcController@PostAddEl');
	$elements = $this->GetElements();
	//возвращаем шаблон, передаем  значения по умолчанию для формы и флаг нового элемента 
	return view('stc/addElement',['types'=>$types,'elements'=>$elements, 'def'=>$default,'opt' =>'new']);
  }
  public function PostAddEl(Request $request)
  {   //сохранение элемента
	  //Проверяем уникальность id, фильтруем форму
	  $this->validate($request, ['NewId'=>'integer|unique:elements,id','ElName' => 'max:50','DepJSONdata' => 'json']);	
	  //Добавляем запись. Связи храним в json
	  $elements = new Elements(array(
									 'id'=>$request->NewId*1,
									 'type_id'=>$request->ElType*1, 
  									 'dependencies'=>$request->DepJSONdata,
 									 'name'=>strip_tags($request->ElName)));
	  $ifUsed = $request->IfUsedData;							 
	  //Для if отмечаем использование yes/no чтобы предотвратить их использование > 1 раза   
	  if(!empty($ifUsed)){
          $ArrIfUsed = json_decode($ifUsed, true);
		  foreach ($ArrIfUsed as $k=>$v)
		  {
			  $el = Elements::find($k);
			  if (!$el->ifUsed)
				 $el->ifUsed=''.$v; 
			  else 
				 $el->ifUsed='b';
			  $el->save();
		  }
		}
	  //Транзакция
	  $saved = $elements->save();
	  if ($saved)
	  //отправляем обратно с msg во флэш-сессии
	  return redirect()->back()->with('msg', 'Элемент успешно добавлен!');
		else 
	  return redirect()->back()->with('msg', 'Ошибка записи в БД');
  }
  public function PostUpdEl(Request $request)
  {	  //сохранение измененного элемента
	  $this->validate($request, ['NewId'=>'integer','ElName' => 'max:50','ElId' => 'integer', 'DepJSONdata' => 'json']);	
	  $curID = $request->CurId*1;
	  $newID = $request->NewId*1;
	  //Если id не изменился, обновляем текущую сущность
	  if ($curID === $newID)
	  { 
	  $element = Elements::find($curID);
	  $element->update(array('type_id'=>$request->ElType*1, 
  							 'dependencies'=>$request->DepJSONdata,
 							 'name'=>strip_tags($request->ElName)));
	  }else{
		  //Если изменился, новая запись, следим за уникальностью id, старую затираем, не комильфо конечно
		  $this->validate($request, ['NewId'=>'unique:elements,id']);
		  Elements::destroy($curID);
		  $element = new Elements(array(
									 'id'=>$newID,
									 'type_id'=>$request->ElType*1, 
  									 'dependencies'=>$request->DepJSONdata,
 									 'name'=>strip_tags($request->ElName)));
		}
	  $ifUsed = $request->IfUsedData;
		//Снова отмечаем yes/no
	  if(!empty($ifUsed)){
          $ArrIfUsed = json_decode($ifUsed, true);
		  foreach ($ArrIfUsed as $k=>$v)
		  {
			  $el = Elements::find($k);
			  if (!$el->ifUsed)
				 $el->ifUsed=''.$v; 
			  elseif ($el->ifUsed!==$v)
				 $el->ifUsed='b';
			  $el->save();
		  }
		}
		//транзакция
	  $saved = $element->save();
	  if ($saved)
		//по именованному маршруту на новую запись
	  return redirect()->route('element_view', $newID)->with('msg', 'Элемент успешно изменен!');
		else 
	  return redirect()->back()->with('msg', 'Ошибка записи в БД');
  }
  public function PostJSON(Request $request)
  { //в процессе
	  return 0;
  }
  public function ElManage($id)
  { //метод редактирования элемента
	$types = $elements = $current = array(); 
	$typesDB = ElTypes::get();	
	foreach($typesDB as $k=>$v){
	$types[$v->id]=$v->name;
	}
	$elements = $this->GetElements();
	//выбираем сущность текущего элемента
	$curEl = Elements::find($id);
	//в шаблон передаем в т.ч. связи элемента 
	$default = array('id'=>$id,'NewId'=>$id,'tId'=>$curEl->type_id,'name'=>$curEl->name,'dep'=>$curEl->dependencies,'action'=>'stcController@PostUpdEl');
  return view('stc/addElement',['types'=>$types,'elements'=>$elements,'def'=>$default,'opt' =>'man']);
  }
  private function getKey($length = 1)
  {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$key = '';
	$c = strlen($chars);
 
	for ($i = 0; $i < $length; ++$i)
		$key .= substr($chars, (mt_rand() % $c), 1);
	return $key;
  }
  private function GetElements($edit = true)
  {
	//метод получения элементов для главной и для добавления/редактирования ($edit=true)
	$elements = $eContent = array();
	$elementsDB = Elements::orderBy($edit ? 'type_id':'id','asc')
						  ->get();
		foreach($elementsDB as $k=>$v){
				$tName = $v->type->name;
				$tId  = $v->type_id;
				$eName = $v->name;
				$eId = $v->id;
				$ifUsed = $v->ifUsed;
				$deps = $v->dependencies;			
				$eContent['ifUsed']='';
				$eContent['type_id']=$tId;
				if ($tId == 5 && $edit) 
					continue;
				if (!$edit)
				{	
				//зависимости для таблицы
					$eContent['tName']=$tName;
					$eContent['name']=$eName;
					$depList = '';
					if($deps){
						$depArr = json_decode($deps, true);
						foreach ($depArr as $d=>$e){
							$depList.=$d.", ";
			 			}
					}
					$eContent['depList']=substr($depList,0,-2);
				}
				else
				{
					switch($tId)
					{
						//собираем информативные имена для элементов
						case 5:$eContent['name']=$tName; break;
						case 1:$eContent['name']=$tName; break;
						case 2:
						case 3:$eContent['name']=$tName." ".$eName; $eContent['ifUsed']=$v->ifUsed ? $v->ifUsed : ''; break;
						case 4:$eContent['name']=$tName." (id:".$eId.")"; break;
					}
				}
				$elements[$eId] = $eContent;
				unset($elemContent);
		}
	return $elements;	
   }
}