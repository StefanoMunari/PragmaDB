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
		$old_tipof=$_POST["old_tipo"];
		$tipof=$_POST["tipo"];
		$old_descf=$_POST["old_desc"];
		$descf=$_POST["desc"];
		$old_implementatof=$_POST["old_implementato"];
		$implementatof=$_POST["implementato"];
		$old_eseguitof=$_POST["old_eseguito"];
		$eseguitof=$_POST["eseguito"];
		$old_esitof=$_POST["old_esito"];
		$esitof=$_POST["esito"];
		$old_correlatof=$_POST["old_correlato"];
		$requi1f=$_POST["requi1"]; //Requisito Correlato (Validazione)
		$requi2f=$_POST["requi2"]; //Requisito Correlato (Sistema)
		$pkgf=$_POST["pkg"];; //Package Correlato (Integrazione)
		$metf=""; //Metodi correlati
		$old_metf=$_POST["lista_old_met"];
		$old_met_array=explode(",", $old_metf);
		$num_metf=$_POST["num_met"];
		for($i=1;$i<=$num_metf;$i++){
			$temp=$_POST["met$i"];
			$metf="$metf"."$temp".",";
		}
		$met_array=explode(",", $metf);
		$modifica_met=false;
		foreach($met_array as $attuale){
			if(!(in_array($attuale, $old_met_array))){
				$modifica_met=true;
			}
		}
		foreach($old_met_array as $vecchio){
			if(!(in_array($vecchio, $met_array))){
				$modifica_met=true;
			}
		}
		$timestampf=$_POST["timestamp"];
		$err_no_modifica=false;
		$err_desc=false;
		$err_requi=false;
		$err_requi_doppio=false;
		$err_pkg=false;
		$err_pkg_doppio=false;
		$err_met=false;
		$errori=0;
		$conn=sql_conn();
		$descf=mysql_escape_string($descf);
		if(($tipof==$old_tipof) && ($descf==$old_descf) && ($implementatof==$old_implementatof) && ($eseguitof==$old_eseguitof) && ($esitof==$old_esitof) && ($modifica_met==false)){
			if(($tipof=="Validazione" && $requi1f==$old_correlatof) || ($tipof=="Sistema" && $requi2f==$old_correlatof) || ($tipof=="Integrazione" && $pkgf==$old_correlatof) || ($tipof=="Unita" && $modifica_met==false)){
				$err_no_modifica=true;
				$errori++;
			}
		}
		if($tipof=="Validazione"){
			if($requi1f=="N/D"){
				$err_requi=true;
				$errori++;
			}
			else{
				$query="SELECT COUNT(*)
						FROM Test t
						WHERE t.tipo='$tipof' AND t.Requisito='$requi1f' AND t.CodAuto<>'$id'";
				$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$row=mysql_fetch_row($ris);
				if($row[0]>0){
					$err_requi_doppio=true;
					$errori++;
				}
			}
		}
		elseif($tipof=="Sistema"){
			if($requi2f=="N/D"){
				$err_requi=true;
				$errori++;
			}
			else{
				$query="SELECT COUNT(*)
						FROM Test t
						WHERE t.tipo='$tipof' AND t.Requisito='$requi2f' AND t.CodAuto<>'$id'";
				$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$row=mysql_fetch_row($ris);
				if($row[0]>0){
					$err_requi_doppio=true;
					$errori++;
				}
			}
		}
		elseif($tipof=="Integrazione"){
			if($pkgf=="N/D"){
				$err_pkg=true;
				$errori++;
			}
			else{
				$query="SELECT COUNT(*)
						FROM Test t
						WHERE t.Package='$pkgf' AND t.CodAuto<>'$id'";
				$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$row=mysql_fetch_row($ris);
				if($row[0]>0){
					$err_pkg_doppio=true;
					$errori++;
				}
			}
		}
		elseif(($tipof=="Unita") && ($num_metf==0)){
			$err_met=true;
			$errori++;
		}
		if($descf==null){
			$err_desc=true;
			$errori++;
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
			if($err_desc){
echo<<<END

					<li>Descrizione: NON INSERITA</li>
END;
			}
			if($err_requi){
echo<<<END

					<li>Requisito: NON INDICATO</li>
END;
			}
			if($err_requi_doppio){
echo<<<END

					<li>Requisito: UN TEST E' GIA' PRESENTE PER IL REQUISITO</li>
END;
			}
			if($err_pkg){
echo<<<END

					<li>Componente: NON INDICATO</li>
END;
			}
			if($err_pkg_doppio){
echo<<<END

					<li>Componente: UN TEST E' GIA' PRESENTE PER IL COMPONENTE</li>
END;
			}
			if($err_met){
echo<<<END

					<li>Metodi: NON INDICATI</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Test/modificatest.php?id=$id">Riprova</a>.</p>
END;
		}
		else{
			$timestamp_query="SELECT t.Time
							  FROM Test t
							  WHERE t.CodAuto='$id'";
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
				<p>Il test è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Test/modificatest.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query1="CALL modifyTest('$id',";
					if($tipof==$old_tipof){
						$query1=$query1."null,";
					}
					else{
						$query1=$query1."'$tipof',";
					}
					if($descf==$old_descf){
						$query1=$query1."null,";
					}
					else{
						$query1=$query1."'$descf',";
					}
					if($implementatof==$old_implementatof){
						$query1=$query1."null,";
					}
					else{
						$query1=$query1."'$implementatof',";
					}
					if($eseguitof==$old_eseguitof){
						$query1=$query1."null,";
					}
					else{
						$query1=$query1."'$eseguitof',";
					}
					if($esitof==$old_esitof){
						$query1=$query1."null,";
					}
					else{
						$query1=$query1."'$esitof',";
					}
					if($tipof=="Validazione"){
						if($requi1f!=$old_correlatof){
							$query1=$query1."'$requi1f',";
						}
						else{
							$query1=$query1."0,";
						}
					}
					elseif($tipof=="Sistema"){
						if($requi2f!=$old_correlatof){
							$query1=$query1."'$requi2f',";
						}
						else{
							$query1=$query1."0,";
						}
					}
					else{
						$query1=$query1."null,";
					}
					if($tipof=="Integrazione"){
						if($pkgf!=$old_correlatof){
							$query1=$query1."'$pkgf')";
						}
						else{
							$query1=$query1."0)";
						}
					}
					else{
						$query1=$query1."null)";
					}
					$query1=mysql_query($query1,$conn) or fail("Query fallita: Modifica Test Fallita - ".mysql_error($conn));
					if(($tipof=="Unita") && ($modifica_met==true)){
						$query2="CALL modifyTestMetodi('$id','$metf')";
						$query2=mysql_query($query2,$conn) or fail("Query fallita: Modifica Metodi Correlati Fallita - ".mysql_error($conn));
					}
					$title="Test Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il test è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Test/test.php">Torna a Test</a>.</p>
END;
				}	
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>Il test è stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Test/test.php">Torna a Test</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$queryTipo="SELECT t.Tipo
					FROM Test t
					WHERE t.CodAuto='$id'";
		$tipo=mysql_query($queryTipo,$conn) or fail("Query fallita: ".mysql_error($conn));
		$tipo=mysql_fetch_row($tipo);
		$tipo=$tipo[0];
		if($tipo=="Validazione"){
			$query="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Tipo, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Requisito
					FROM Test t JOIN Requisiti r ON t.Requisito=r.CodAuto
					WHERE t.CodAuto='$id'";
		}
		elseif($tipo=="Sistema"){
			$query="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Tipo, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Requisito
					FROM Test t JOIN Requisiti r ON t.Requisito=r.CodAuto
					WHERE t.CodAuto='$id'";
		}
		else{
			$query="SELECT t.CodAuto, t.IdTest, t.Tipo, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Package
					FROM Test t
					WHERE t.CodAuto='$id'";
		}
		$test=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$testdb=mysql_fetch_row($test);
		if($testdb[0]==$id){
			$title="Modifica Test - $testdb[1]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<script type="text/javascript" src="$absurl/Test/script_test.js"></script>
				<h2>Modifica - $testdb[1]</h2>
				<div id="form">
					<form action="$absurl/Test/modificatest.php?id=$id" method="post" onsubmit="return validateForm()">
						<fieldset>
							<p>
								<label for="tipo1">Tipo*:</label>
END;
			if($testdb[2]=="Validazione"){
echo<<<END

								<input type="radio" id="tipo1" name="tipo" value="Validazione" onchange="val()" checked="checked"/> Validazione
END;
			}
			else{
echo<<<END

								<input type="radio" id="tipo1" name="tipo" value="Validazione" onchange="val()" /> Validazione
END;
			}
			if($testdb[2]=="Sistema"){
echo<<<END

								<input type="radio" id="tipo2" name="tipo" value="Sistema" onchange="sis()" checked="checked" /> Sistema
END;
			}
			else{
echo<<<END

								<input type="radio" id="tipo2" name="tipo" value="Sistema" onchange="sis()" /> Sistema
END;
			}
			if($testdb[2]=="Integrazione"){
echo<<<END

								<input type="radio" id="tipo3" name="tipo" value="Integrazione" onchange="int()" checked="checked" /> Integrazione
END;
			}
			else{
echo<<<END

								<input type="radio" id="tipo3" name="tipo" value="Integrazione" onchange="int()" /> Integrazione
END;
			}
			if($testdb[2]=="Unita"){
echo<<<END

								<input type="radio" id="tipo4" name="tipo" value="Unita" onchange="uni()" checked="checked" /> Unità
END;
			}
			else{
echo<<<END

								<input type="radio" id="tipo4" name="tipo" value="Unita" onchange="uni()" /> Unità
END;
			}
echo<<<END

							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$testdb[3]</textarea>
							</p>
							<p>
								<label for="implementato">Implementato*:</label>
END;
			if($testdb[4]=="0"){
echo<<<END

								<input type="radio" id="implementato1" name="implementato" value="0" onchange="notImpl()" checked="checked" /> Non Implementato
								<input type="radio" id="implementato2" name="implementato" value="1" onchange="impl()" /> Implementato
							</p>
							<p id="esegs" style="display:none;">
END;
			}
			else{
echo<<<END

								<input type="radio" id="implementato1" name="implementato" value="0" onchange="notImpl()" /> Non Implementato
								<input type="radio" id="implementato2" name="implementato" value="1" onchange="impl()" checked="checked" /> Implementato
							</p>
							<p id="esegs" style="display:block;">
END;
			}
echo<<<END

								<label for="eseguito">Eseguito*:</label>
END;
			if($testdb[5]=="0"){
echo<<<END

								<input type="radio" id="eseguito1" name="eseguito" value="0" onchange="notExec()" checked="checked" /> Non Eseguito
								<input type="radio" id="eseguito2" name="eseguito" value="1" onchange="exec()" /> Eseguito
							</p>
							<p id="esits" style="display:none;">
END;
			}
			else{
echo<<<END

								<input type="radio" id="eseguito1" name="eseguito" value="0" onchange="notExec()" /> Non Eseguito
								<input type="radio" id="eseguito2" name="eseguito" value="1" onchange="exec()" checked="checked" /> Eseguito
							</p>
							<p id="esits" style="display:block;">
END;
			}
echo<<<END

								<label for="esito">Esito*:</label>
END;
			if($testdb[6]=="0"){
echo<<<END

								<input type="radio" id="esito1" name="esito" value="0" checked="checked" /> Non Superato
								<input type="radio" id="esito2" name="esito" value="1" /> Superato
END;
			}
			else{
echo<<<END

								<input type="radio" id="esito1" name="esito" value="0" /> Non Superato
								<input type="radio" id="esito2" name="esito" value="1" checked="checked" /> Superato
END;
			}
echo<<<END

							</p>
END;
			if($testdb[2]=="Validazione"){
echo<<<END

							<p id="requis1" style="display:block;">
END;
			}
			else{
echo<<<END

							<p id="requis1" style="display:none;">
END;
			}
echo<<<END

								<label for="requi1">Requisito Correlato*:</label>
								<select id="requi1" name="requi1">
									<option value="N/D">N/D</option>
END;
							
			$conn=sql_conn();
			//$query_ord="CALL sortForest('Requisiti')";
			$query="SELECT r.CodAuto, r.IdRequisito
					FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
					WHERE r.CodAuto NOT IN (SELECT t.Requisito FROM Test t WHERE t.Tipo='Validazione' AND t.Requisito IS NOT NULL AND t.CodAuto<>'$id')
					ORDER BY h.Position"; //Query che calcola i requisiti disponibili
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			$requi=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			while($row=mysql_fetch_row($requi)){
				if($row[0]!=null){
					if(($testdb[2]=="Validazione") && ($row[0]==$testdb[7])){
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
END;
			if($testdb[2]=="Sistema"){
echo<<<END

							<p id="requis2" style="display:block;">
END;
			}
			else{
echo<<<END

							<p id="requis2" style="display:none;">
END;
			}
echo<<<END

								<label for="requi2">Requisito Correlato*:</label>
								<select id="requi2" name="requi2">
									<option value="N/D">N/D</option>
END;
			$conn=sql_conn();
			//$query_ord="CALL sortForest('Requisiti')";
			$query="SELECT r.CodAuto, r.IdRequisito
					FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
					WHERE r.CodAuto NOT IN (SELECT t.Requisito FROM Test t WHERE t.Tipo='Sistema' AND t.Requisito IS NOT NULL AND t.CodAuto<>'$id')
					ORDER BY h.Position"; //Query che calcola i requisiti disponibili
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			$requi=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			while($row=mysql_fetch_row($requi)){
				if($row[0]!=null){
					if(($testdb[2]=="Sistema") && ($row[0]==$testdb[7])){
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
END;
			if($testdb[2]=="Integrazione"){
echo<<<END

							<p id="pkgs" style="display:block;">
END;
			}
			else{
echo<<<END

							<p id="pkgs" style="display:none;">
END;
			}
echo<<<END
								<label for="pkg">Componente Correlato*:</label>
								<select id="pkg" name="pkg">
									<option value="N/D">N/D</option>
END;
			//Stampo la lista dei package disponibili
			$query="SELECT p.CodAuto, p.PrefixNome
					FROM Package p
					WHERE p.CodAuto NOT IN (SELECT t.Package FROM Test t WHERE t.Package IS NOT NULL AND t.CodAuto<>'$id')
					ORDER BY p.PrefixNome"; //Query che calcola i package disponibili
			$pack=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			while($row=mysql_fetch_row($pack)){
				if($row[0]!=null){
					if(($testdb[2]=="Integrazione") && ($row[0]==$testdb[7])){
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
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
END;
			if($testdb[2]=="Unita"){
echo<<<END

							<p id="mets" style="display:block;">
END;
			}
			else{
echo<<<END

							<p id="mets" style="display:none;">
END;
			}
echo<<<END

								<label for="met1">Metodi Correlati*:</label>
								<select id="met0" name="met0" style="display:none;">
									<option value="N/D">N/D</option>
END;
			//Stampo la lista dei metodi disponibili
			$conn=sql_conn();
			$query="SELECT m.CodAuto, CONCAT(c.PrefixNome,'::',m.Nome), m.ReturnType
					FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
					ORDER BY CONCAT(c.PrefixNome,'::',m.Nome)"; //Query che calcola i metodi disponibili
			$met=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			while($row=mysql_fetch_row($met)){
				if($row[0]!=null){
					$query_par="SELECT p.Nome, p.Tipo
								FROM Parametro p
								WHERE p.Metodo='$row[0]'";
					$par=mysql_query($query_par,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END

									<option value="$row[0]">$row[1](
END;
					if($riga=mysql_fetch_row($par)){
echo<<<END
$riga[0]: $riga[1]
END;
					}
					while($riga=mysql_fetch_row($par)){
echo<<<END
, $riga[0]: $riga[1]
END;
					}
echo<<<END
)
END;
					if($row[2]!=null){
echo<<<END
: $row[2]
END;
					}
echo<<<END
</option>
END;
				}
			}
echo<<<END

								</select>
END;
			//Parte selezione multipla metodi correlati
			$conn=sql_conn();
			$query="SELECT m.CodAuto, CONCAT(c.PrefixNome,'::',m.Nome), m.ReturnType
					FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
					ORDER BY CONCAT(c.PrefixNome,'::',m.Nome)"; //Query che calcola i metodi disponibili
			$tutti_met_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_met=array();
			while($row=mysql_fetch_row($tutti_met_query)){
				if($row[0]!=null){
					$query_par="SELECT p.Nome, p.Tipo
								FROM Parametro p
								WHERE p.Metodo='$row[0]'";
					$par=mysql_query($query_par,$conn) or fail("Query fallita: ".mysql_error($conn));
					$totalString="$row[1](";
					if($riga=mysql_fetch_row($par)){
						$totalString=$totalString."$riga[0]: $riga[1]";
					}
					while($riga=mysql_fetch_row($par)){
						$totalString=$totalString.", $riga[0]: $riga[1]";
					}
					$totalString=$totalString.")";
					if($row[2]!=null){
						$totalString=$totalString.": $row[2]";
					}
				}
				$tutti_met["$totalString"]=$row[0];
			}
			$query="SELECT m.CodAuto, CONCAT(c.PrefixNome,'::',m.Nome), m.ReturnType
					FROM (TestMetodi tm JOIN Metodo m ON tm.CodMet=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
					WHERE tm.CodTest='$id'
					ORDER BY CONCAT(c.PrefixNome,'::',m.Nome)"; //Query che calcola i metodi disponibili
			$met_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$met=array();
			$listaoldmet="";
			$i=0;
			while($row=mysql_fetch_row($met_query)){
				if($row[0]!=null){
					$query_par="SELECT p.Nome, p.Tipo
								FROM Parametro p
								WHERE p.Metodo='$row[0]'";
					$par=mysql_query($query_par,$conn) or fail("Query fallita: ".mysql_error($conn));
					$totalString="$row[1](";
					if($riga=mysql_fetch_row($par)){
						$totalString=$totalString."$riga[0]: $riga[1]";
					}
					while($riga=mysql_fetch_row($par)){
						$totalString=$totalString.", $riga[0]: $riga[1]";
					}
					$totalString=$totalString.")";
					if($row[2]!=null){
						$totalString=$totalString.": $row[2]";
					}
				}
				$met["$totalString"]=$row[0];
				$listaoldmet=($listaoldmet.$row[0]).",";
			}
			$met_rimanenti=array();
			foreach($tutti_met as $tiid => $tcod){
				$trovato=false;
				foreach($met as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
						$trovato=true;
					}
				}
				if($trovato==false){
					$met_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($met as $iid => $cod){
				$i++;
echo<<<END

								<select id="met$i" name="met$i" onchange="multiple_sel(9,$i)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($met_rimanenti as $riid => $rcod){
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

								<select id="met$i" name="met$i" onchange="multiple_sel(9,$i)">
									<option value="N/D">N/D</option>
END;
			foreach($met_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
END;
			$i--;
echo<<<END

							<input type="hidden" id="num_met" name="num_met" value="$i" />
							<input type="hidden" id="old_tipo" name="old_tipo" value="$testdb[2]" />
							<input type="hidden" id="old_desc" name="old_desc" value="$testdb[3]" />
							<input type="hidden" id="old_implementato" name="old_implementato" value="$testdb[4]" />
							<input type="hidden" id="old_eseguito" name="old_eseguito" value="$testdb[5]" />
							<input type="hidden" id="old_esito" name="old_esito" value="$testdb[6]" />
							<input type="hidden" id="old_correlato" name="old_correlato" value="$testdb[7]" />
							<input type="hidden" id="lista_old_met" name="lista_old_met" value="$listaoldmet" />
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
			$title="Modifica Test - Test Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il test con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Test/test.php">Torna a Test</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>