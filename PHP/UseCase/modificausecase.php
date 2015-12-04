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
		$nomef=$_POST["nome"];
		$diagf=$_POST["diag"];
		$descf=$_POST["desc"];
		$pref=$_POST["pre"];
		$postf=$_POST["post"];
		$padref=$_POST["padre"];
		$princf=$_POST["princ"];
		$inclusionif=$_POST["inclusioni"];
		$estensionif=$_POST["estensioni"];
		$altef=$_POST["alte"];
		$attorif="";
		$requif="";
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
		$num_attoref=$_POST["num_attore"];
		$timestampf=$_POST["timestamp"];
		$err_nome=false;
		$err_desc=false;
		$err_pre=false;
		$err_post=false;
		$err_padre=false;
		$err_princ=false;
		$err_attore=false;
		$errori=0;
		if($nomef==null){
			$err_nome=true;
			$errori++;
		}
		if($descf==null){
			$err_desc=true;
			$errori++;
		}
		if($pref==null){
			$err_pre=true;
			$errori++;
		}
		if($postf==null){
			$err_post=true;
			$errori++;
		}
		if($padref==$id){
			$err_padre=true;
			$errori++;
		}
		if($princf==null){
			$err_princ=true;
			$errori++;
		}
		if($num_attoref<1){
			$err_attore=true;
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
			if($err_pre){
echo<<<END

					<li>Precondizioni: NON INDICATE</li>
END;
			}
			if($err_post){
echo<<<END

					<li>Postcondizioni: NON INDICATE</li>
END;
			}
			if($err_post){
echo<<<END

					<li>Padre: INDICATO COME PADRE LO UC STESSO O I SUOI FIGLI</li>
END;
			}
			if($err_princ){
echo<<<END

					<li>Scenario Principale: NON INDICATO</li>
END;
			}
			if($err_attore){
echo<<<END

					<li>Attori Coinvolti: NECESSARIO INDICARE ALMENO UN ATTORE</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/UseCase/modificausecase.php?id=$id">Riprova</a>.</p>
END;
		}
		else{
			for($i=1;$i<=$num_attoref;$i++){
				$temp=$_POST["attore$i"];
				$attorif="$attorif"."$temp".",";
			}
			$nomef=mysql_escape_string($nomef);
			$diagf=mysql_escape_string($diagf);
			$descf=mysql_escape_string($descf);
			$pref=mysql_escape_string($pref);
			$postf=mysql_escape_string($postf);
			$princf=mysql_escape_string($princf);
			$inclusionif=mysql_escape_string($inclusionif);
			$estensionif=mysql_escape_string($estensionif);
			$altef=mysql_escape_string($altef);
			$conn=sql_conn();
			$timestamp_query="SELECT u.Time
							  FROM UseCase u
							  WHERE u.CodAuto='$id'";
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
				<p>Lo use case è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/UseCase/modificausecase.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL modifyUseCase('$id','$nomef',";
					if($diagf==null){
						$query=$query."null,";
					}
					else{
						$query=$query."'$diagf',";
					}
					$query=$query."'$descf','$pref','$postf',";
					if($padref=="N/D"){
						$query=$query."null,";
					}
					else{
						$query=$query."'$padref',";
					}
					$query=$query."'$princf',";
					if($inclusionif==null){
						$query=$query."null,";
					}
					else{
						$query=$query."'$inclusionif',";
					}
					if($estensionif==null){
						$query=$query."null,";
					}
					else{
						$query=$query."'$estensionif',";
					}
					if($altef==null){
						$query=$query."null,";
					}
					else{
						$query=$query."'$altef',";
					}
					if($modifica_requi==false){
						$query=$query."null,";
					}
					else{
						$query=$query."'$requif',";
					}
					$query=$query."'$attorif','$_SESSION[user]')";
					$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
					$title="Use Case Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Lo use case è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/UseCase/usecase.php">Torna a UseCase</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>Lo use case è stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/UseCase/usecase.php">Torna a UseCase</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT u.CodAuto,u.IdUC,u.Nome,u.Diagramma,u.Descrizione,u.Precondizioni,u.Postcondizioni,u.Padre,u.ScenarioPrincipale,u.Inclusioni,u.Estensioni,u.ScenariAlternativi,u.Time
				FROM UseCase u
				WHERE u.CodAuto='$id'";
		$title="Modifica Use Case";
		$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$ucdb=mysql_fetch_row($uc);
		if($ucdb[0]==$id){
			$title="Modifica Use Case - $ucdb[1]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Modifica Use Case</h2>
				<div id="form">
					<form action="$absurl/UseCase/modificausecase.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="300" value="$ucdb[2]" />
							</p>
							<p>
								<label for="nome">Diagramma:</label>
								<input type="text" id="diag" name="diag" maxlength="50" value="$ucdb[3]" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$ucdb[4]</textarea>
							</p>
							<p>
								<label for="pre">Precondizioni*:</label>
								<textarea rows="2" cols="0" id="pre" name="pre" maxlength="10000">$ucdb[5]</textarea>
							</p>
							<p>
								<label for="post">Postcondizioni*:</label>
								<textarea rows="2" cols="0" id="post" name="post" maxlength="10000">$ucdb[6]</textarea>
							</p>
							<p>
								<label for="padre">Padre:</label>
								<select id="padre" name="padre">
									<option value="N/D">N/D</option>
END;
			//$query_ord="CALL sortForest('UseCase')";
			$query="SELECT u.CodAuto, u.IdUC
					FROM _MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto
					ORDER BY h.Position";
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			while($row=mysql_fetch_row($uc)){
				if($row[0]!=null){
					if($row[0]==$ucdb[7]){
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
							<p>Non indicare come padre lo UC stesso o i suoi discendenti!</p>
							<p>
								<label for="princ">Scenario Principale*:</label>
								<textarea rows="2" cols="0" id="princ" name="princ" maxlength="10000">$ucdb[8]</textarea>
							</p>
							<p>
								<label for="inclusioni">Inclusioni:</label>
								<textarea rows="2" cols="0" id="inclusioni" name="inclusioni" maxlength="10000">$ucdb[9]</textarea>
							</p>
							<p>
								<label for="estensioni">Estensioni:</label>
								<textarea rows="2" cols="0" id="estensioni" name="estensioni" maxlength="10000">$ucdb[10]</textarea>
							</p>
							<p>
								<label for="alte">Scenari Alternativi:</label>
								<textarea rows="2" cols="0" id="alte" name="alte" maxlength="10000">$ucdb[11]</textarea>
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="attores">
								<label for="attore1">Attori Coinvolti:</label>
END;
			$query="SELECT a.CodAuto, a.Nome
					FROM Attori a
					ORDER BY a.Nome";
			$tutti_attori_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_attori=array();
			while($row=mysql_fetch_row($tutti_attori_query)){
				$tutti_attori["$row[1]"]=$row[0];
			}
			$query="SELECT a.CodAuto, a.Nome
					FROM AttoriUC auc JOIN Attori a ON auc.Attore=a.CodAuto
					WHERE auc.UC='$id'
					ORDER BY a.Nome";
			$attori_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$attori=array();
			$i=0;
			while($row=mysql_fetch_row($attori_query)){
				$attori["$row[1]"]=$row[0];
			}
			$attori_rimanenti=array();
			foreach($tutti_attori as $tnom => $tcod){
				$trovato=false;
				foreach($attori as $conf_nom => $conf_cod){
					if($conf_cod==$tcod){
						$trovato=true;
					}
				}
				if($trovato==false){
					$attori_rimanenti["$tnom"]=$tcod;
				}
			}
			foreach($attori as $nom => $cod){	
				$i++;
echo<<<END

								<select id="attore$i" name="attore$i" onchange="multiple_sel(1,$i)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$nom</option>
END;
				foreach($attori_rimanenti as $rnom => $rcod){
echo<<<END

									<option value="$rcod">$rnom</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$i++;
echo<<<END

								<select id="attore$i" name="attore$i" onchange="multiple_sel(1,$i)">
									<option value="N/D">N/D</option>
END;
			foreach($attori_rimanenti as $rnom => $rcod){
echo<<<END

									<option value="$rcod">$rnom</option>
END;
			}
echo<<<END

								</select>
							</p>
							<p id="requis">
								<label for="requi1">Requisiti Correlati:</label>
END;
			//$query_ord="CALL sortForest('Requisiti')";
			$query="SELECT r.CodAuto, r.IdRequisito
					FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
					ORDER BY h.Position";
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_requi_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_requi=array();
			while($row=mysql_fetch_row($tutti_requi_query)){
				$tutti_requi["$row[1]"]=$row[0];
			}
			$query="SELECT r.CodAuto, r.IdRequisito
					FROM RequisitiUC ruc JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON ruc.CodReq=r.CodAuto
					WHERE ruc.UC='$id'
					ORDER BY h.Position";
			$requi_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$requi=array();
			$listaoldrequi="";
			$j=0;
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
				$j++;
echo<<<END

								<select id="requi$j" name="requi$j" onchange="multiple_sel(2,$j)">
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
			$j++;
echo<<<END

								<select id="requi$j" name="requi$j" onchange="multiple_sel(2,$j)">
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
echo<<<END

							<input type="hidden" id="num_requi" name="num_requi" value="$j" />
							<input type="hidden" id="num_attore" name="num_attore" value="$i" />
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
			$title="Modifica Use Case - Use Case Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Lo use case con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/UseCase/usecase.php">Torna a Use Case</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>