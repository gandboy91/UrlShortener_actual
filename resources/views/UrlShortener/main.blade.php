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
    <script src="{{ asset('js/react/react.min.js') }}" defer></script>
    <script src="{{ asset('js/react/react-dom.min.js') }}" defer></script> 
    <script src="{{ asset('js/react/browser.min.js') }}" defer></script>
    <script src="{{ asset('js/react/axios.min.js') }}" defer></script>     
    <style type="text/css">
        tr,th,td { border: 1px solid #BBBBBB; text-align: center;}
        h1 {color:#007324;}
        div {padding-left:0!important;}
        input,select,button {border-radius: 5px; padding: 5px;}
        #Shorten {
            background-color:#67DB8C;
            border: 2px solid #02BA3C;
            font: bold 18px verdana;
        } 
        #Shorten:hover {
            background-color:#6BBAFF;
        }
        .message, .msg{
            font: bold 16px verdana; 
            color: #ffec04;
            padding-left: 10px;
        }
        .UrlInput {
            margin-bottom: 20px; font: bold 16px verdana; 
            width:80%;
        }
        .UrlOutput {
            margin-top: 20px; 
            font: bold 16px verdana; 
            background-color: #82FF0F;
            min-width: 300px;
        }
    </style>
</head>
<body>
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <h1 style="margin-bottom:40px;">my URL-shortener</h1>    
        <div class="row" style="height:40px;">
        @if (session('message'))
        <p class="msg" style="padding-left:0;">{{ session('message') }}</p>
        @endif
        </div>
        <div class="row">
            <div class="col-sm-10"><b>Введите оригинальный URL с указанием протокола (например&nbsp;http://&nbsp;или&nbsp;https://)</b></div> 
            <div class="col-sm-2"></div> 
        </div><br/>
        <div id="UrlShortener">
        </div>
    </div>
    <div class="col-sm-2"></div>    
    <script src = "{{ asset('js/minUrl/urlReact.js') }}" defer type="text/babel" ></script>
</body>
</html>