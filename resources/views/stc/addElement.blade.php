<!--resources/views/stcaddElement.blade.php-->

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>@if ($opt=='new')Добавление элемента 
		@elseif ($opt=='man')Редактирование элемента
		@endif
	</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
	<script src="{{ asset('js/jquery.min.js') }}" defer></script> 
	<script src="{{ asset('js/stc/addEl.js') }}" defer></script> 
</head>
<body>
	<div class="col-sm-2"></div>
	<div class="col-sm-8">
	@if (count($errors) > 0)
  	<ul style="font-weight: bold; color: red;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
	@endif
	@if (session('msg'))
	<ul style="font-weight: bold; color: green;">
        <li>{{ session('msg') }}</li>
    </ul>
	@endif
	<a href='/stc'>
		<h3 style='background-color: #DCEF39; display: inline; padding: 5px; border: 2px solid green;'>к списку</h3>
	</a>
	<h2 style="font-weight: bold; margin-bottom: 20px;">@if ($opt=='new')Добавление элемента 
		@elseif ($opt=='man')Редактирование элемента
		@endif
	</h2>	
	{{ Form::open(array('action' => $def['action'], 'method' => 'post', 'role' => 'form', 'id' => 'AddElForm')) }}
	<div class="row">
		<div class="col-sm-1" style="padding: 0;"><b>№ эл:</b></div>
		<div class="col-sm-1" style="padding: 0;">{{Form::text('NewId',$def['NewId'],array('id'=>'ElId','style'=>'width:40px;'))}}</div>
		<div class="col-sm-3"><b>Тип элемента:</b></div>	
		<div class="col-sm-7">
		{{ Form::select('ElType',$types,$def['tId'],array('id'=>'TypeSelect','style'=>'width:200px;')) }}
		</div>
	</div><br />
	<div class="row">
		<div class="col-sm-5"><b>Имя элемента(для if вопрос):</b></div>	
		<div class="col-sm-7">
		{{ Form::text('ElName',$def['name'],array('id'=>'ElementName','style'=>'width:200px;')) }}
		</div>
	</div>
	<h3>Зависимости (от каких элементов и как зависит текущий элемент)</h3>
	<div  id ="DepBlock">
		<div class="row">
		<div class="col-sm-2"><b>Элемент:</b></div>	
		<div class="col-sm-3">
		<select id ="DepElSelect">
		@if ($elements)	
			@foreach ($elements as $k => $el)
		    <option value="{{$k}}" @if($el['ifUsed']) used="{{$el['ifUsed']}}" @if($el['ifUsed']=='b') disabled @endif @endif type="{{ $el['type_id'] }}">{{ $el['name'] }}</option>
			@endforeach
		@endif
		</select>
		
		</div>
		<div class="col-sm-2"><b>Тип связи:</b></div>
		<div class="col-sm-2">
		{{ Form::select('DepSelect',array('b'=>'bind','y'=>'YES branch','n'=>"NO branch"),2,array('id'=>'DepSelect','class'=>'')) }}
		</div>
		<div class="col-sm-3">
		<button id="addDep"> + зависимость</button> 	
		</div>
		</div>
		<br />
		<div class="row">
			<div class="col-sm-5">
			<h3 style="display: inline;">Зависимости : </h3><button id="JSONDep">готово</button>
			<p>Добавьте нужные зависимости и нажмите 'готово'.</p><br />
			</div>
			<div class="col-sm-7">
			<span id="DepReady"></span>
			<ul id ="dependencies">
			</ul>
			</div>
		</div>
		{{ Form::hidden('CurId', $def['id'],array('id'=>'CurId')) }}
		{{ Form::hidden('DepJSONdata', $def['dep'],array('id'=>'DepJSONdata')) }}
		{{ Form::hidden('IfUsedData', '',array('id'=>'IfUsedData')) }}
	</div>
	<button id="SubmEl"><b>@if($opt=='new') Добавить элемент! @else Сохранить изменения @endif</b></button>
	{{ Form::close() }}
	</div>
	<div class="col-sm-2"></div>
<!-- {!! dd($def) !!}  -->
</body>
</html>




