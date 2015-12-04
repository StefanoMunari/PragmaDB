function exec(){
	var esi=document.getElementById('esits');
	esi.style.display="block";
}

function impl(){
	var exe=document.getElementById('esegs');
	exe.style.display="block";
}

function int(){
	resetRequi1();
	resetRequi2();
	var pkg=document.getElementById('pkgs');
	pkg.style.display="block";
	resetMets();
}

function notExec(){
	var esiF=document.getElementById('esito1');
	var esi=document.getElementById('esits');
	esiF.checked=true;
	esits.style.display="none";
}

function notImpl(){
	var esiF=document.getElementById('esito1');
	var esi=document.getElementById('esits');
	esiF.checked=true;
	esits.style.display="none";
	var exeF=document.getElementById('eseguito1');
	var exe=document.getElementById('esegs');
	exeF.checked=true;
	exe.style.display="none";
}

function resetForm(){
	var esi=document.getElementById('esits');
	esits.style.display="none";
	var exe=document.getElementById('esegs');
	exe.style.display="none";
	resetRequi1();
	resetRequi2();
	resetPkg();
	resetMets();
}

function resetMets(){
	var mets=document.getElementById('mets');
	mets.style.display="none";
	var ind=document.getElementById('num_met');
	var num=Number(ind.value);
	for(var i=2;i<=(num+1);i++){
		var sel=document.getElementById('met'+i);
		mets.removeChild(sel);
	}
	ind.value=0;
	var zero=document.getElementById('met0');
	var first=document.getElementById('met1');
	while(first.options[0]){
		first.removeChild(first.options[0]);
	}
	for(var i=0;zero.options[i];i++){
		var op=document.createElement("option");
		op.text=zero.options[i].text;
		op.value=zero.options[i].value;
		op.selected=zero.options[i].selected;
		first.appendChild(op);
	}
}

function resetPkg(){
	var pkg=document.getElementById('pkgs');
	pkg.style.display="none";
	var first=document.getElementById('pkg');
	first.options[0].selected=true;
}

function resetRequi1(){
	var requi=document.getElementById('requis1');
	requi.style.display="none";
	var first=document.getElementById('requi1');
	first.options[0].selected=true;
}

function resetRequi2(){
	var requi=document.getElementById('requis2');
	requi.style.display="none";
	var first=document.getElementById('requi2');
	first.options[0].selected=true;
}

function sis(){
	var requi=document.getElementById('requis2');
	requi.style.display="block";
	resetRequi1();
	resetPkg();
	resetMets();
}

function val(){
	var requi=document.getElementById('requis1');
	requi.style.display="block";
	resetRequi2();
	resetPkg();
	resetMets();
}

function validateForm(){
	var type1=document.getElementById('tipo1');
	var type2=document.getElementById('tipo2');
	var type3=document.getElementById('tipo3');
	var type4=document.getElementById('tipo4');
	var desc=document.getElementById('desc');
	var requi1=document.getElementById('requi1');
	var requi2=document.getElementById('requi2');
	var pkg=document.getElementById('pkg');
	var met=document.getElementById('met1');
	var aler="";
	var errors=0;
	if((!type1.checked) && (!type2.checked) && (!type3.checked) && (!type4.checked)){
		aler=aler+"TIPO: Non selezionato\n";
		errors++;
	}
	if(desc.value==""){
		aler=aler+"DESCRIZIONE: Non inserita\n";
		errors++;
	}
	if((type1.checked &&  requi1.options[requi1.selectedIndex].text=="N/D") || (type2.checked && requi2.options[requi2.selectedIndex].text=="N/D")){
		aler=aler+"REQUISITO: Non indicato\n";
		errors++;
	}
	if((type3.checked) && (pkg.options[pkg.selectedIndex].text=="N/D")){
		aler=aler+"COMPONENTE: Non indicato\n";
		errors++;
	}
	if((type4.checked) && (met.options[met.selectedIndex].text=="N/D")){
		aler=aler+"METODO: Non indicato\n";
		errors++;
	}
	if(errors==0){
		document.getElementById("form").submit();
	}
	else{
		alert(aler);
		return false;
	}
}

function uni(){
	resetRequi1();
	resetRequi2();
	resetPkg();
	var met=document.getElementById('mets');
	met.style.display="block";
}
