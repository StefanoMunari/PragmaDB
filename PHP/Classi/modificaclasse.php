<?php

require('../Functions/mysql_fun.php');
require('../Functions/page_builder.php');
require('../Functions/urlLab.php'); 

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	if(isset($_REQUEST['submit'])){
		$id=$_GET['id'];
		$old_nomef=$_POST["old_nome"];
		$nomef=$_POST["nome"];
		$old_prefixNomef=$_POST["old_prefixNome"];
		$old_descf=$_POST["old_desc"];
		$descf=$_POST["desc"];
		$old_utilf=$_POST["old_util"];
		$utilf=$_POST["util"];
		$old_padref=$_POST["old_padre"];
		$padref=$_POST["padre"];
		$old_diagf=$_POST["old_diag"];
		$diagf=$_POST["diag"];
		$prefixPadre="";
		$cluf=""; //Eredita Da
		$old_cluf=$_POST["lista_old_clu"];
		$old_clu_array=explode(",", $old_cluf);
		$num_cluf=$_POST["num_clu"];
		for($i=1;$i<=$num_cluf;$i++){
			$temp=$_POST["clu$i"];
			$cluf="$cluf"."$temp".",";
		}
		$clu_array=explode(",", $cluf);
		$modifica_clu=false;
		foreach($clu_array as $attuale){
			if(!(in_array($attuale, $old_clu_array))){
				$modifica_clu=true;
			}
		}
		foreach($old_clu_array as $vecchio){
			if(!(in_array($vecchio, $clu_array))){
				$modifica_clu=true;
			}
		}
		$cldf=""; //Ereditano da lei
		$old_cldf=$_POST["lista_old_cld"];
		$old_cld_array=explode(",", $old_cldf);
		$num_cldf=$_POST["num_cld"];
		for($i=1;$i<=$num_cldf;$i++){
			$temp=$_POST["cld$i"];
			$cldf="$cldf"."$temp".",";
		}
		$cld_array=explode(",", $cldf);
		$modifica_cld=false;
		foreach($cld_array as $attuale){
			if(!(in_array($attuale, $old_cld_array))){
				$modifica_cld=true;
			}
		}
		foreach($old_cld_array as $vecchio){
			if(!(in_array($vecchio, $cld_array))){
				$modifica_cld=true;
			}
		}
		$cltf=""; //Relazioni IN
		$old_cltf=$_POST["lista_old_clt"];
		$old_clt_array=explode(",", $old_cltf);
		$num_cltf=$_POST["num_clt"];
		for($i=1;$i<=$num_cltf;$i++){
			$temp=$_POST["clt$i"];
			$cltf="$cltf"."$temp".",";
		}
		$clt_array=explode(",", $cltf);
		$modifica_clt=false;
		foreach($clt_array as $attuale){
			if(!(in_array($attuale, $old_clt_array))){
				$modifica_clt=true;
			}
		}
		foreach($old_clt_array as $vecchio){
			if(!(in_array($vecchio, $clt_array))){
				$modifica_clt=true;
			}
		}
		$clqf=""; //Relazioni OUT
		$old_clqf=$_POST["lista_old_clq"];
		$old_clq_array=explode(",", $old_clqf);
		$num_clqf=$_POST["num_clq"];
		for($i=1;$i<=$num_clqf;$i++){
			$temp=$_POST["clq$i"];
			$clqf="$clqf"."$temp".",";
		}
		$clq_array=explode(",", $clqf);
		$modifica_clq=false;
		foreach($clq_array as $attuale){
			if(!(in_array($attuale, $old_clq_array))){
				$modifica_clq=true;
			}
		}
		foreach($old_clq_array as $vecchio){
			if(!(in_array($vecchio, $clq_array))){
				$modifica_clq=true;
			}
		}
		$requif=""; //Requisiti Correlati
		$old_requif=$_POST["lista_old_requi"];
		$old_requi_array=explode(",", $old_requif);
		$num_requif=$_POST["num_requi"];
		for($i=1;$i<=$num_requif;$i++){
			$temp=$_POST["requi$i"];
			$requif="$requif"."$temp".",";
		}
		$requi_array=explode(",", $requif);
		$modifica_requi=false;
		foreach($requi_array as $attuale){
			if(!(in_array($attuale, $old_requi_array))){
				$modifica_requi=true;
			}
		}
		foreach($old_requi_array as $vecchio){
			if(!(in_array($vecchio, $requi_array))){
				$modifica_requi=true;
			}
		}
		$timestampf=$_POST["timestamp"];
		$err_no_modifica=false;
		$err_nome=false;
		$err_desc=false;
		$err_padre=false;
		$err_presente=false;
		$errori=0;
		if(($nomef==$old_nomef) && ($descf==$old_descf) && ($utilf==$old_utilf) && ($padref==$old_padref) && ($diagf==$old_diagf) && ($modifica_clu==false) && ($modifica_cld==false) && ($modifica_clt==false) && ($modifica_clq==false) && ($modifica_requi==false)){
			$err_no_modifica=true;
			$errori++;
		}
		if($nomef==null){
			$err_nome=true;
			$errori++;
		}
		if($descf==null){
			$err_desc=true;
			$errori++;
		}
		$conn=sql_conn();
		$query="SELECT p.RelationType,p.PrefixNome
				FROM Package p
				WHERE p.CodAuto='$padref'";
		$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$row=mysql_fetch_row($ris);
		if($row[0]==null){
			$err_padre=true;
			$errori++;
		}
		else{
			$prefixPadre="$row[1]::";
		}
		$nomef=mysql_escape_string($nomef);
		$descf=mysql_escape_string($descf);
		$utilf=mysql_escape_string($utilf);
		$diagf=mysql_escape_string($diagf);
		if($old_prefixNomef!=($prefixPadre.$nomef)){
			$conn=sql_conn();
			$query="SELECT COUNT(*)
					FROM Classe c
					WHERE c.PrefixNome='$prefixPadre"."$nomef'";
			$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$row=mysql_fetch_row($ris);
			if($row[0]>0){
				$err_presente=true;
				$errori++;
			}
		}
		if($errori>0){
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento dei seguenti campi:</h2>
				<ul>
END;
			if($err_no_modifica){
echo<<<END

					<li>Nessun campo è stato modificato!</li>
END;
			}
			if($err_nome){
echo<<<END

					<li>Nome: NON INSERITO</li>
END;
			}
			if($err_desc){
echo<<<END

					<li>Descrizione: NON INSERITA</li>
END;
			}
			if($err_padre){
echo<<<END

					<li>Padre: IL PADRE INDICATO NON ESISTE</li>
END;
			}
			if($err_presente){
echo<<<END

					<li>LA CLASSE E' GIA' PRESENTE NEL DB</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$id">Riprova</a>.</p>
END;
		}
		else{
			$conn=sql_conn();
			$timestamp_query="SELECT c.Time
							  FROM Classe c
							  WHERE c.CodAuto='$id'";
			$timestamp_query=mysql_query($timestamp_query,$conn) or fail("Query fallita: ".mysql_error($conn));
			if($row=mysql_fetch_row($timestamp_query)){
				$timestamp_db=$row[0];
				$timestamp_db=strtotime($timestamp_db);
				if($timestampf<$timestamp_db){
					$title="Errore";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>La classe è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					if(($nomef!=$old_nomef) || ($descf!=$old_descf) || ($utilf!=$old_utilf) ||($padref!=$old_padref) || ($diagf!=$old_diagf)){
						$query1="CALL modifyClasse('$id',";
						if($nomef==$old_nomef){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$nomef',";
						}
						if($old_prefixNomef==($prefixPadre.$nomef)){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$prefixPadre"."$nomef',";
						}
						if($descf==$old_descf){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$descf',";
						}
						if($utilf==$old_utilf){
							$query1=$query1."0,";
						}
						else{
							$query1=$query1."'$utilf',";
						}
						if($padref==$old_padref){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$padref',";
						}
						if($diagf==$old_diagf){
							$query1=$query1."0)";
						}
						else{
							$query1=$query1."'$diagf')";
						}
						$query1=mysql_query($query1,$conn) or fail("Query fallita: Modifica Classe Fallita - ".mysql_error($conn));
					}
					if($modifica_clu==true){
						if($num_cluf>0){
							$query2="CALL modifyEreditaDa('$id','$cluf')";
						}
						else{
							$query2="CALL removeEreditaDa('$id')";
						}
						$query2=mysql_query($query2,$conn) or fail("Query fallita: Modifica EreditaDa Fallita - ".mysql_error($conn));
					}
					if($modifica_cld==true){
						if($num_cldf>0){
							$query3="CALL modifyEreditataDa('$id','$cldf')";
						}
						else{
							$query3="CALL removeEreditataDa('$id')";
						}
						$query3=mysql_query($query3,$conn) or fail("Query fallita: Modifica Ereditano da lei Fallita - ".mysql_error($conn));
					}
					if($modifica_clt==true){
						if($num_cltf>0){
							$query4="CALL modifyRelazioneA('$id','$cltf')";
						}
						else{
							$query4="CALL removeRelazioneA('$id')";
						}
						$query4=mysql_query($query4,$conn) or fail("Query fallita: Modifica Relazioni IN Fallita - ".mysql_error($conn));
					}
					if($modifica_clq==true){
						if($num_clqf>0){
							$query5="CALL modifyRelazioneDa('$id','$clqf')";
						}
						else{
							$query5="CALL removeRelazioneDa('$id')";
						}
						$query5=mysql_query($query5,$conn) or fail("Query fallita: Modifica Relazioni OUT Fallita - ".mysql_error($conn));
					}
					if($modifica_requi==true){
						if($num_requif>0){
							$query6="CALL modifyClasseRequisiti('$id','$requif')";
						}
						else{
							$query6="CALL removeClasseRequisiti('$id')";
						}
						$query6=mysql_query($query6,$conn) or fail("Query fallita: Modifica Requisiti Correlati Fallita - ".mysql_error($conn));
					}
					$title="Classe Modificata";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>La classe è stata modificata con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
				}	
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>La classe è stata eliminata da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT c.CodAuto,c.PrefixNome,c.Nome,c.Descrizione,c.Utilizzo,c.ContenutaIn,c.UML
				FROM Classe c
				WHERE c.CodAuto='$id'";
		$cl=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$cldb=mysql_fetch_row($cl);
		if($cldb[0]==$id){
			$title="Modifica Classe - $cldb[1]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Modifica - $cldb[1]</h2>
				<div id="form">
					<form action="$absurl/Classi/modificaclasse.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="100" value="$cldb[2]" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$cldb[3]</textarea>
							</p>
							<p>
								<label for="util">Utilizzo:</label>
								<textarea rows="2" cols="0" id="util" name="util" maxlength="10000">$cldb[4]</textarea>
							</p>
							<p>
								<label for="padre">ContenutaIn*:</label>
								<select id="padre" name="padre">
END;
			$conn=sql_conn();
			$query="SELECT p.CodAuto,p.PrefixNome
					FROM Package p
					ORDER BY p.PrefixNome"; //Query per recuperare l'id di tutti i package
					//in modo che $row[0] sia l'id e che $row[1] sia il [prefisso::]nome 
			$father=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			while($row=mysql_fetch_row($father)){
				if($row[0]!=null){
					if($row[0]==$cldb[5]){
echo<<<END

									<option value="$row[0]" selected="selected">$row[1]</option>
END;
					}
					else{
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
					}
				}
			}
echo<<<END

								</select>
							</p>
							<p>
								<label for="diag">Diagramma:</label>
								<input type="text" id="diag" name="diag" maxlength="50" value="$cldb[6]"/>
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="clus">
								<label for="clu1">Eredita Da:</label>
END;
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM Classe c
					WHERE c.CodAuto<>'$id'
					ORDER BY c.PrefixNome";
			$tutti_clu_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_clu=array();
			while($row=mysql_fetch_row($tutti_clu_query)){
				$tutti_clu["$row[1]"]=$row[0];
			}
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM EreditaDa ed JOIN Classe c ON ed.Padre=c.CodAuto
					WHERE ed.Figlio='$id'
					ORDER BY c.PrefixNome";
			$clu_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$clu=array();
			$listaoldclu="";
			$i=0;
			while($row=mysql_fetch_row($clu_query)){
				$clu["$row[1]"]=$row[0];
				$listaoldclu=($listaoldclu.$row[0]).",";
			}
			$clu_rimanenti=array();
			foreach($tutti_clu as $tiid => $tcod){
				$trovato=false;
				foreach($clu as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
					$trovato=true;
					}
				}
				if($trovato==false){
					$clu_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($clu as $iid => $cod){
				$i++;
echo<<<END

								<select id="clu$i" name="clu$i" onchange="multiple_sel(5,$i)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($clu_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$i++;
echo<<<END

								<select id="clu$i" name="clu$i" onchange="multiple_sel(5,$i)">
									<option value="N/D">N/D</option>
END;
			foreach($clu_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
							<p id="clds">
								<label for="cld1">Ereditano da lei:</label>
END;
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM Classe c
					WHERE c.CodAuto<>'$id'
					ORDER BY c.PrefixNome";
			$tutti_cld_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_cld=array();
			while($row=mysql_fetch_row($tutti_cld_query)){
				$tutti_cld["$row[1]"]=$row[0];
			}
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM EreditaDa ed JOIN Classe c ON ed.Figlio=c.CodAuto
					WHERE ed.Padre='$id'
					ORDER BY c.PrefixNome";
			$cld_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$cld=array();
			$listaoldcld="";
			$j=0;
			while($row=mysql_fetch_row($cld_query)){
				$cld["$row[1]"]=$row[0];
				$listaoldcld=($listaoldcld.$row[0]).",";
			}
			$cld_rimanenti=array();
			foreach($tutti_cld as $tiid => $tcod){
				$trovato=false;
				foreach($cld as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
					$trovato=true;
					}
				}
				if($trovato==false){
					$cld_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($cld as $iid => $cod){
				$j++;
echo<<<END

								<select id="cld$j" name="cld$j" onchange="multiple_sel(6,$j)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($cld_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$j++;
echo<<<END

								<select id="cld$j" name="cld$j" onchange="multiple_sel(6,$j)">
									<option value="N/D">N/D</option>
END;
			foreach($cld_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
							<p id="clts">
								<label for="clt1">Classi Correlate - IN:</label>
END;
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM Classe c
					WHERE c.CodAuto<>'$id'
					ORDER BY c.PrefixNome";
			$tutti_clt_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_clt=array();
			while($row=mysql_fetch_row($tutti_clt_query)){
				$tutti_clt["$row[1]"]=$row[0];
			}
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM Relazione r JOIN Classe c ON r.Da=c.CodAuto
					WHERE r.A='$id'
					ORDER BY c.PrefixNome";
			$clt_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$clt=array();
			$listaoldclt="";
			$k=0;
			while($row=mysql_fetch_row($clt_query)){
				$clt["$row[1]"]=$row[0];
				$listaoldclt=($listaoldclt.$row[0]).",";
			}
			$clt_rimanenti=array();
			foreach($tutti_clt as $tiid => $tcod){
				$trovato=false;
				foreach($clt as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
					$trovato=true;
					}
				}
				if($trovato==false){
					$clt_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($clt as $iid => $cod){
				$k++;
echo<<<END

								<select id="clt$k" name="clt$k" onchange="multiple_sel(7,$k)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($clt_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$k++;
echo<<<END

								<select id="clt$k" name="clt$k" onchange="multiple_sel(7,$k)">
									<option value="N/D">N/D</option>
END;
			foreach($clt_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
							<p id="clqs">
								<label for="clq1">Classi Correlate - OUT:</label>
END;
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM Classe c
					WHERE c.CodAuto<>'$id'
					ORDER BY c.PrefixNome";
			$tutti_clq_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_clq=array();
			while($row=mysql_fetch_row($tutti_clq_query)){
				$tutti_clq["$row[1]"]=$row[0];
			}
			$query="SELECT c.CodAuto,c.PrefixNome
					FROM Relazione r JOIN Classe c ON r.A=c.CodAuto
					WHERE r.Da='$id'
					ORDER BY c.PrefixNome";
			$clq_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$clq=array();
			$listaoldclq="";
			$l=0;
			while($row=mysql_fetch_row($clq_query)){
				$clq["$row[1]"]=$row[0];
				$listaoldclq=($listaoldclq.$row[0]).",";
			}
			$clq_rimanenti=array();
			foreach($tutti_clq as $tiid => $tcod){
				$trovato=false;
				foreach($clq as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
					$trovato=true;
					}
				}
				if($trovato==false){
					$clq_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($clq as $iid => $cod){
				$l++;
echo<<<END

								<select id="clq$l" name="clq$l" onchange="multiple_sel(8,$l)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($clq_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$l++;
echo<<<END

								<select id="clq$l" name="clq$l" onchange="multiple_sel(8,$l)">
									<option value="N/D">N/D</option>
END;
			foreach($clq_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
							<p id="requis">
								<label for="requi1">Requisiti Correlati:</label>
END;
			//$query_ord="CALL sortForest('Requisiti')";
			$query="SELECT r.CodAuto,r.IdRequisito
					FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
					ORDER BY h.Position";
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_requi_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_requi=array();
			while($row=mysql_fetch_row($tutti_requi_query)){
				$tutti_requi["$row[1]"]=$row[0];
			}
			$query="SELECT r.CodAuto,r.IdRequisito
					FROM RequisitiClasse rc JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON rc.CodReq=r.CodAuto
					WHERE rc.CodClass='$id'
					ORDER BY h.Position";
			$requi_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$requi=array();
			$listaoldrequi="";
			$m=0;
			while($row=mysql_fetch_row($requi_query)){
				$requi["$row[1]"]=$row[0];
				$listaoldrequi=($listaoldrequi.$row[0]).",";
			}
			$requi_rimanenti=array();
			foreach($tutti_requi as $tiid => $tcod){
				$trovato=false;
				foreach($requi as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
					$trovato=true;
					}
				}
				if($trovato==false){
					$requi_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($requi as $iid => $cod){
				$m++;
echo<<<END

								<select id="requi$m" name="requi$m" onchange="multiple_sel(2,$m)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($requi_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$m++;
echo<<<END

								<select id="requi$m" name="requi$m" onchange="multiple_sel(2,$m)">
									<option value="N/D">N/D</option>
END;
			foreach($requi_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
END;
			$i--;
			$j--;
			$k--;
			$l--;
			$m--;
echo<<<END

							<input type="hidden" id="num_clu" name="num_clu" value="$i" />
							<input type="hidden" id="num_cld" name="num_cld" value="$j" />
							<input type="hidden" id="num_clt" name="num_clt" value="$k" />
							<input type="hidden" id="num_clq" name="num_clq" value="$l" />
							<input type="hidden" id="num_requi" name="num_requi" value="$m" />
							<input type="hidden" id="old_prefixNome" name="old_prefixNome" value="$cldb[1]" />
							<input type="hidden" id="old_nome" name="old_nome" value="$cldb[2]" />
							<input type="hidden" id="old_desc" name="old_desc" value="$cldb[3]" />
							<input type="hidden" id="old_util" name="old_util" value="$cldb[4]" />
							<input type="hidden" id="old_padre" name="old_padre" value="$cldb[5]" />
							<input type="hidden" id="old_diag" name="old_diag" value="$cldb[6]" />
							<input type="hidden" id="lista_old_clu" name="lista_old_clu" value="$listaoldclu" />
							<input type="hidden" id="lista_old_cld" name="lista_old_cld" value="$listaoldcld" />
							<input type="hidden" id="lista_old_clt" name="lista_old_clt" value="$listaoldclt" />
							<input type="hidden" id="lista_old_clq" name="lista_old_clq" value="$listaoldclq" />
							<input type="hidden" id="lista_old_requi" name="lista_old_requi" value="$listaoldrequi" />
							<input type="hidden" id="timestamp" name="timestamp" value="$timestamp" />
							<p>
								<input type="submit" id="submit" name="submit" value="Modifica" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
END;
		}
		else{
			$title="Modifica Classe - Classe Non Trovata";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La classe con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>