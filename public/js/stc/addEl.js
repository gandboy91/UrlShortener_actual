document.addEventListener("DOMContentLoaded", function(){
  var AddElForm, ElTypeInd, ElType, ElName, DepBlock, DepElSelect, DepElType, changeEv, IfUsed,
  DepToJSON, AddDepBtn, DepList, JSONDBtn, DepJSONdata, DepReady, warnSave, warnOk, SubmEl, IfUsedData;
  		AddElForm = this.getElementById('AddElForm');
  		ElType = this.getElementById('TypeSelect');
	 	ElName = this.getElementById('ElementName');
	 	DepElSelect = this.getElementById('DepElSelect');
	 	DepSelect = this.getElementById('DepSelect');
	 	DepBlock = this.getElementById('DepBlock');
	 	AddDepBtn = this.getElementById('addDep');
	 	DepList = this.getElementById('dependencies');
	 	JSONDBtn = this.getElementById('JSONDep');
	 	DepJSONdata = this.getElementById('DepJSONdata');
	 	IfUsedData = this.getElementById('IfUsedData');
		DepReady = this.getElementById('DepReady');
		SubmEl = this.getElementById('SubmEl');
		warnSave = '<span style="color:red"><b>Сохраните изменения!</b></span>';
	 	warnOk = '<span style="color:green"><b>Связи добавлены!</b></span>';	 	
	 	DepToJSON = {};
	 	IfUsed = {};
	 	changeEv = new Event("change");
	 	ElTypeInd = +ElType.value;
	 	if(DepJSONdata.value.length!==0){
	 		DepToJSON=JSON.parse(DepJSONdata.value);
	 		for (var dkey in DepToJSON) {
	 			var dVal = DepToJSON[dkey];
 				var defDES = DepElSelect.querySelector('option[value ="'+dkey+'"]');
 				var defDS = DepSelect.querySelector('option[value ="'+dVal+'"]');
  				defDES.disabled = true;
				defDES.selected = false;
				if (dVal!=='b')
				{IfUsed[dkey] = dVal;}
				var defDep = document.createElement('li');
				defDep.setAttribute('key',dkey);
				defDep.setAttribute('dtype',dVal);
				defDepTxt = "<span style='color:red;cursor:pointer'><b>x</b></span>  |" 
		 		+ defDES.text+"| -- " + defDS.text + " -->";
				defDep.innerHTML = defDepTxt;
				delDep = defDep.querySelector('span');
	  			delDep.addEventListener('click', function(e){
	  				var key = +this.parentNode.getAttribute('key');
	  				var dtype = this.parentNode.getAttribute('dtype');
	  				var CurDepEl = DepElSelect.querySelector('option[value ="'+key+'"]');
	  				var used = CurDepEl.getAttribute('used');
	  				this.parentNode.remove();
	  				if(used=='b'){
	  						if (dtype=='y'){
	  							IfUsed[key]='n';
	  							CurDepEl.setAttribute('used','n');			
	  						} else {
	  							IfUsed[key]='y';
	  							CurDepEl.setAttribute('used','y');
	  						}
	  				} else 
	  				{
	  					if(used==dtype){
	  						CurDepEl.setAttribute('used','');
	  					}
	  					delete IfUsed[key];
	  				}
	  				delete DepToJSON[key];
	  				CurDepEl.disabled = false;
	  				DepReady.innerHTML = warnSave;
	   			});
	   			if(Object.keys(IfUsed).length !== 0){
		 			IfUsedData.value = (JSON.stringify(IfUsed));
		 		}
	   			DepList.appendChild(defDep);
	   			DepElSelect.dispatchEvent(changeEv);
			}	 				
	 	}
	 	ElType.addEventListener('change',function(){
	 		ElTypeInd = +this.value
		 	if([2,3].indexOf(ElTypeInd) === -1){
		 		ElName.disabled = true;
		 		ElName.value = '';
		 	}
		 	else
		 	{
		 		ElName.disabled = false;
		 	}
		 	if(ElTypeInd === 1){
		 		DepBlock.style.display = 'none';
		 	}
		 	else
		 	{
		 		DepBlock.style.display = 'block';
		  	}
	 	})
	 	DepElSelect.addEventListener('change',function(){
	 		if(this.value!=='')
	 		{
	 			var UsedAttr = this.options[this.selectedIndex].getAttribute('used');
		 		DepElType = +this.options[this.selectedIndex].getAttribute('type');
			 	if(ElTypeInd !== 1)
			 	{
			 		DepSelect.style.display = 'block';
			 		if (DepElType === 3)
			 			{	var yOpt = DepSelect.querySelector('option[value = "y"]');
			 				var nOpt = DepSelect.querySelector('option[value = "n"]');
			 				if (+UsedAttr!==0)
			 				{
			 					if (UsedAttr==='n'){
			 						DepSelect.value='y';
			 						yOpt.style.display = 'block';
			 						nOpt.style.display = 'none';
			 					} else if (UsedAttr==='y'){
			 						DepSelect.value='n';
			 						nOpt.style.display = 'block';
			 						yOpt.style.display = 'none';
			 					}
			 				}
			 				else
			 				{	
			 					DepSelect.value='y';
			 					yOpt.style.display = 'block';
			 					nOpt.style.display = 'block';
			 				}
			 				DepSelect.querySelector('option[value = "b"]').style.display = 'none';	
			 			}
			 			else
			 			{
			 				DepSelect.value='b';
			 				DepSelect.querySelector('option[value = "y"]').style.display = 'none';
			 				DepSelect.querySelector('option[value = "n"]').style.display = 'none';
			 				DepSelect.querySelector('option[value = "b"]').style.display = 'block';
			 			}
			 	}
			 	else
			 	{
			 		DepBlock.style.display = 'none';
			 	}
		 	}
	 	})
	 	AddDepBtn.onclick = function(e){
	 		e.preventDefault();
	 		DepReady.innerHTML = warnSave;
	 		if(DepElSelect.value!=='')
	 		{
	 			var DepElId = DepElSelect.value;
		 		var curDES = DepElSelect.options[DepElSelect.selectedIndex];
		 		var curDS = DepSelect.options[DepSelect.selectedIndex];
		 		if (DepElType === 3){
	 				IfUsed[DepElId] = DepSelect.value;
	 			}
	 			DepToJSON[DepElId] = DepSelect.value; 
		 		var NewDepTxt = "<span style='color:red;cursor:pointer'><b>x</b></span>  |" 
		 		+ curDES.text+"| -- " + curDS.text + " -->";
		 		var newDep = document.createElement('li');
		 		newDep.setAttribute('key',curDES.value);
		 		newDep.setAttribute('dtype',curDS.value);
	  			newDep.innerHTML = NewDepTxt;
	  			delDep = newDep.querySelector('span');
	  			delDep.addEventListener('click', function(e){
	  				var key = +this.parentNode.getAttribute('key');
	  				var dtype = this.parentNode.getAttribute('dtype');
	  				var CurDepEl = DepElSelect.querySelector('option[value ="'+key+'"]');
	  				var used = CurDepEl.getAttribute('used');
	  				this.parentNode.remove();
	  				if(used=='b'){
	  				if (dtype=='y'){
	  					IfUsed[key]='n';
	  					CurDepEl.setAttribute('used','n');			
	  					} else {
	  					IfUsed[key]='y';
	  					CurDepEl.setAttribute('used','y');
	  					}
	  				} else 
	  				{
	  					if(used==dtype){
	  						CurDepEl.setAttribute('used','');
	  					}	  						
	  					delete IfUsed[key];
	  				}
	  				delete DepToJSON[key];
	  				CurDepEl.disabled = false;
	  				DepReady.innerHTML = warnSave;
	   			});
		 		DepList.appendChild(newDep);
		 		curDES.disabled = true;
		 		curDES.selected = false;
		 		DepElSelect.dispatchEvent(changeEv);	
	 		}
	 	};
	 	JSONDBtn.onclick = function(e){
	 		e.preventDefault();
	 		if(Object.keys(DepToJSON).length !== 0)	{
		 		DepJSONdata.value = (JSON.stringify(DepToJSON));
		 		DepReady.innerHTML = warnOk;	
		 		DepReady.style.display = 'block';	
		 		if(Object.keys(IfUsed).length !== 0){
		 			IfUsedData.value = (JSON.stringify(IfUsed));
		 		}
	 		}
	 	}
	 	SubmEl.onclick = function(e){
	 		e.preventDefault();
	 		if([2,3].indexOf(ElTypeInd) !== -1){
	 			if(ElName.value.length===0){
	 				alert('Введите имя элемента'); return 0;
	 			}
	 		}
	 		if(DepJSONdata.value.length===0 && ElTypeInd !== 1){
					alert('Добавьте и сохраните Связи'); return 0;
	 		}
	 		AddElForm.submit();
	 	}
});

