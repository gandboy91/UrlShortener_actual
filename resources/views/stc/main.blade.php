<!--resources/views/stcaddElement.blade.php-->

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Конструктор зависимостей</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
	<script src="{{ asset('js/jquery.min.js') }}" defer></script> 
	<script src="{{ asset('js/stc/main.js') }}" defer></script> 
	<style type="text/css">
		tr,th,td {
			border: 1px solid #BBBBBB; 
			text-align: center;
		}
	</style>
</head>
<body>
	<div class="col-sm-2"></div>
	<div class="col-sm-8">
	<h2><b>Конструктор зависимостей</b></h2>
	@if (session('msg'))
	<ul style="font-weight: bold; color: green;">
        <li>{{ session('msg') }}</li>
    </ul>
	@endif
	<br />
	<a href='/stc/AddEl' style="font-size: 16px; background-color: #DCEF39; border: 2px solid green; padding: 5px;">
		<b> + элемент</b></a>
	<table style="width: 100%;">
		<caption><h3 style="color:blue;">Все элементы и их зависимости</h3></caption>
		<tr>
			<th>ID элемента</th>
	        <th>Тип</th>
	        <th>Имя/Вопрос</th>
	        <th>Зависит от</th>
	        <th>Редактировать</th>
	        <th>Удалить</th>
		</tr>
		@if ($elements)	
			@foreach ($elements as $k => $el)
		    <tr>
		        <td>{{$k}}</td>
		        <td>{{$el['tName']}}</td>
		        <td>{{$el['name']}}</td>
		        <td>{{$el['depList']}}</td>
		        <td><a href="stc/element/{{$k}}"><b>manage</b></a></td>
		        <td><a href="stc/delEl/{{$k}}" style="color:red"><b>X</b></a></td>
		    </tr>
		    @endforeach
		@endif    
	</table>	
	</div>
	<div class="col-sm-2"></div>
</body>
</html>




