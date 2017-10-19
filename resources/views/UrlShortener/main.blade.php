<!--resources/views/stcaddElement.blade.php-->
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="crsft" name="csrf-token" content="{{ csrf_token() }}" />
    <title>my URL-shortener</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/minUrl.css') }}" />
    <script src="{{ asset('js/react/react.min.js') }}" defer></script>
    <script src="{{ asset('js/react/react-dom.min.js') }}" defer></script> 
    <script src="{{ asset('js/react/axios.min.js') }}" defer></script>     
</head>
<body>
    <div class="row" id="wrapper">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <h1 style="margin:15px;">my URL-shortener</h1> 
            <div class="row" style="height:40px;">
            <p id="msg" class="msg text-warning">@if (session('message')){{ session('message') }}@endif</p>
            </div>     
            <div id="UrlShortener">
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>    
    <script src = "{{ asset('js/minUrl/urlReact.js') }}" defer ></script>
</body>
</html>