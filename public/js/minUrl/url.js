document.addEventListener("DOMContentLoaded", function(){
	 var lUrlForm,lUrl,ShrtBtn,myRegEx,AjaxData,UrLenSelect,crsfToken,Resp,ResUrl;
	 //получаем необходимые элементы DOM
	 UrLenSelect = this.getElementById('urLen');
	 lUrlForm = this.getElementById('lUrl');
	 ShrtBtn = this.getElementById('Shorten');
	 ResUrl = this.getElementById('resUrl');
	 crsfToken = this.getElementById('crsft').getAttribute('content');
	 //рег. выражение для предварительного парсинга url 
	 myRegEx = /^(https?:\/\/)([\w\.]+)([\/]?.*)$/; 
	 AjaxData = Resp = {};
	 ResUrl.disabled = true;
	 ShrtBtn.onclick = function(e){ 
	 	e.preventDefault();
		lUrl = lUrlForm.value;
		if(lUrl.length===0){
	 		alert('Введите URL!'); return 0;
	 	}
		urlParts = myRegEx.exec(lUrl);
		if(+urlParts===0){
			alert('некорректный URL! проверьте протокол (http:// или https://) и домен (недопустимые символы).'); return 0;
		}
		AjaxData['lUrl'] = ""+urlParts[0];
		AjaxData['len'] = +UrLenSelect.value;
		AjaxData['csrft'] = ""+crsfToken;
		myAjax(AjaxData);
		
	}
});
function myAjax(data)
{
	 var token = data['csrft'];
	 var ResUrl = document.getElementById('resUrl');
	 var ResMsg = document.getElementById('ResMsg');
	 var warnOk = '<span style="color:green"><b>Успешно!</b></span>';
	 var warnErr = '';
	 var PostQ = "lUrl=" + encodeURIComponent(data['lUrl']) + "&len=" + encodeURIComponent(data['len']);
	 var request = new XMLHttpRequest();
	 request.open('POST', 'minUrl/new', true);
	 request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	 request.setRequestHeader('X-CSRF-TOKEN', token);
	 request.onreadystatechange = function() {
		 //console.log(request.readyState);
     if(request.readyState === 4){ 
		   if(request.status === 200) { 			  
			  Resp = JSON.parse(request.responseText);
			  if(+Resp['success']===1)
			  {
			  	ResMsg.innerHTML = warnOk;
			  	ResUrl.disabled = false;
			  	ResUrl.value = Resp['slug'];
			  }
			  else
			  {
				warnErr = '<span style="color:red"><b>'+ Resp['err'] +'!</b></span>';
				if (+Resp['duplSlug']!==0){
				warnErr += '<br \><span style="color:green"><b>короткая ссылка для него: '+ Resp['duplSlug'] +'</b></span>';	
				}
				ResMsg.innerHTML = warnErr;
			  	ResUrl.value = '';	
			  	ResUrl.disabled = true;
			  }
			  //console.log(Resp);
			}
		  else 
			{
			 alert('Ошибка обработки Ajax запроса.');
			}
		}
	 }
	 request.send(PostQ);
}