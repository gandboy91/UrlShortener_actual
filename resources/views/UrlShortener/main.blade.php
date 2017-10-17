<!--resources/views/stcaddElement.blade.php-->

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta id="crsft" name="csrf-token" content="{{ csrf_token() }}" />
	<title>my URL-shortener</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
	<script src="{{ asset('js/jquery.min.js') }}" defer></script> 
	<script src="{{ asset('js/minUrl/url.js') }}" defer></script> 
	<style type="text/css">
		tr,th,td { border: 1px solid #BBBBBB; text-align: center;}
		h1 {color:#007324;}
		div {padding-left:0!important;}
		input,select,button {border-radius: 5px; padding: 5px;}
		#Shorten {
			background-color:#67DB8C;
			border: 2px solid #02BA3C;
			font: bold 18px verdana;} 
		#Shorten:hover {
			background-color:#6BBAFF;
		}
		#ResMsg {
			font-size:18px;
		}
		.msg {
			font: bold 16px verdana; color: #ffec04;
		}
	</style>
</head>
<body>
	<div class="col-sm-2"></div>
	<div class="col-sm-8">
	<h1 style="margin-bottom:40px;">my URL-shortener</h1>
	<div class="row" style="height:40px;">
    @if (session('message'))
	<p class="msg">{{ session('message') }}</p>
	@endif
    </div>
	<div class="row">
		<div class="col-sm-10"><b>Введите оригинальный URL с указанием протокола (например&nbsp;http://&nbsp;или&nbsp;https://)</b></div> 
		<div class="col-sm-2"></div> 
	</div><br/>
	<div class="row">
		<div class="col-sm-10">{{Form::text('LongUrl','',array('id'=>'lUrl','style'=>'width:100%;'))}}</div> 
		<div class="col-sm-2"></div>
	</div>
	<div class="row" style="margin-top:20px;">
	<div class="col-sm-5">{{Form::text('','',array('id'=>'resUrl','disabled','style'=>'width:100%'))}}
	</div>
	<div id="ResMsg" class="col-sm-7">
	</div>
	</div>
	<div style="margin-top:20px;">
	<button id="Shorten">Сгенерировать</button>
	</div>
	</div>
	<div class="col-sm-2"></div>
</body>
</html>




