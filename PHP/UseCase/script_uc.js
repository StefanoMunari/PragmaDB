function multiple_sel(cod,ind){
	var str="attore";
	if(cod==2){
		str="requi";
	}
	if(cod==3){
		str="uc";
	}
	if(cod==4){
		str="pkg"; //relazioni tra i componenti, usato per Package
	}
	if(cod==5){
		str="clu";
	}
	if(cod==6){
		str="cld";
	}
	if(cod==7){
		str="clt";
	}
	if(cod==8){
		str="clq";
	}
	if(cod==9){
		str="met";
	}
	var cont=0;
	var index=1;
	var sel=document.getElementById(str+ind);
	var sel_text=sel.options[sel.selectedIndex].text;
	var par=document.getElementById(str+"s");
	while(document.getElementById(str+index)){
		index++;
	}
	cont=index-1;
	/*rimuove l'appena selezionato*/
	if(sel_text!="N/D"){
		for(var i=1;i<=cont;i++){
			if(i!=ind){
				var att=document.getElementById(str+i);
				var found=false;
				var del=att.options[0];
				for(var j=0;!found;j++){
					del=att.options[j];
					if(del.text==sel_text){
						found=true;
					}
				}
				att.removeChild(del);
			}
		}
	}
	if(sel.id==(str+cont)){
		/*aggiunge la nuova select*/
		var se=document.createElement("select");
		se.setAttribute("id",str+index);
		se.setAttribute("onchange","multiple_sel("+cod+","+index+")");
		se.setAttribute("name",str+index);
		for(var i=0;sel.options[i];i++){
			if(i!=sel.selectedIndex){
				var op=document.createElement("option");
				op.text=sel.options[i].text;
				op.value=sel.options[i].value;
				se.appendChild(op);
			}
		}
		par.appendChild(se);
		var att=document.getElementById("num_"+str);
		att.setAttribute("value",cont);
	}
	else{
		/*cerca il vecchio selezionato*/
		var old_selection="";
		var old_value="";
		var first=false;
		var prec_index=ind-1;
		if(ind==1){
			prec_index=ind+1;
		}
		var prec=document.getElementById(str+prec_index);
		for(var i=0;(old_selection=="") && (sel.options[i]);i++){
			var found=false;
			for(var j=0;(!found)&&(prec.options[j]);j++){
				if(prec.options[j].text==sel.options[i].text){
					found=true;
				}
			}
			if(!found){
				old_selection=sel.options[i].text;
				old_value=sel.options[i].value;
			}
		}
		/******ripristina il vecchio*****/
		if(old_selection!=""){
			for(var i=1;i<=cont;i++){
				if(i!=ind){
					var att=document.getElementById(str+i);
					var opt=document.createElement("option");
					opt.text=old_selection;
					opt.value=old_value;
					att.appendChild(opt);
				}
			}
		}
		if(sel_text=="N/D"){
			par.removeChild(sel);
			var att=document.getElementById("num_"+str);
			var num=cont-2;
			att.setAttribute("value",num);
			for(var i=ind+1;i<=cont;i++){
				var att=document.getElementById(str+i);
				var newind=i-1;
				att.setAttribute("id",str+newind);
				att.setAttribute("onchange","multiple_sel("+cod+","+newind+")");
				att.setAttribute("name",str+newind);
			}
		}
	}
}