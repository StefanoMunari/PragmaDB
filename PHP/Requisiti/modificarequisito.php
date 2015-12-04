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
		$old_descf=$_POST["old_desc"];
		$descf=$_POST["desc"];
		$old_tipof=$_POST["old_tipo"];
		if(isset($_POST["tipo"])){
			$tipof=$_POST["tipo"];
		}
		$old_importanzaf=$_POST["old_importanza"];
		if(isset($_POST["importanza"])){
			$importanzaf=$_POST["importanza"];
		}
		$old_padref=$_POST["old_padre"];
		$padref=$_POST["padre"];
		$old_statof=$_POST["old_stato"];
		if(isset($_POST["stato"])){
			$statof=$_POST["stato"];
		}
		$old_soddisfattof=$_POST["old_soddisfatto"];
		$soddisfattof=$_POST["soddisfatto"];
		$old_implementatof=$_POST["old_implementato"];
		$implementatof=$_POST["implementato"];
		$old_fontef=$_POST["old_fonte"];
		$fontef=$_POST["fonte"];
		$ucf="";
		$old_ucf=$_POST["lista_old_uc"];
		$old_uc_array=explode(",", $old_ucf);
		$num_ucf=$_POST["num_uc"];
		for($i=1;$i<=$num_ucf;$i++){
			$temp=$_POST["uc$i"];
			$ucf="$ucf"."$temp".",";
		}
		$uc_array=explode(",", $ucf);
		$modifica_uc=false;
		foreach($uc_array as $attuale){
			if(!(in_array($attuale, $old_uc_array))){
				$modifica_uc=true;
			}
		}
		foreach($old_uc_array as $vecchio){
			if(!(in_array($vecchio, $uc_array))){
				$modifica_uc=true;
			}
		}
		$timestampf=$_POST["timestamp"];
		$err_no_modifica=false;
		$err_desc=false;
		$err_tipo=false;
		$err_importanza=false;
		$err_tipo_padre=false;
		$err_importanza_padre=false;
		$err_padre_ricorsivo=false;
		$err_stato=false;
		$err_fonte=false;
		$errori=0;
		if($old_padref==null){
			$old_padref="N/D";
		}
		if((isset($tipof)) && (isset($importanzaf))){
			if(isset($statof)){
				if(($descf==$old_descf) && ($tipof==$old_tipof) && ($importanzaf==$old_importanzaf) && ($padref==$old_padref) && ($statof==$old_statof) && ($soddisfattof==$old_soddisfattof) && ($implementatof==$old_implementatof) && ($fontef==$old_fontef) && ($modifica_uc==false)){
					$err_no_modifica=true;
					$errori++;
				}
			}
			else{
				if(($descf==$old_descf) && ($tipof==$old_tipof) && ($importanzaf==$old_importanzaf) && ($padref==$old_padref) && ($soddisfattof==$old_soddisfattof) && ($implementatof==$old_implementatof) && ($fontef==$old_fontef) && ($modifica_uc==false)){
					$err_no_modifica=true;
					$errori++;
				}
			}
		}
		if($descf==null){
			$err_desc=true;
			$errori++;
		}
		if(!(isset($tipof))){
			$err_tipo=true;
			$errori++;
		}
		if(!(isset($importanzaf))){
			$err_importanza=true;
			$errori++;
		}
		else{
			if($importanzaf=="1"){
				$statof="0";
			}
			else{
				if(!(isset($statof))){
					$err_stato=true;
					$errori++;
				}
			}
		}
		if($padref!="N/D"){
			$conn=sql_conn();
			$query="SELECT r.Tipo+0,r.Importanza+0
					FROM Requisiti r
					WHERE r.CodAuto='$padref'";
			$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$row=mysql_fetch_row($ris);
			if($row[0]!=null){
				if($row[0]!=$tipof){
					$err_tipo_padre=true;
					$errori++;
				}
				if($row[1]>$importanzaf){
					$err_importanza_padre=true;
					$errori++;
				}
			}
			if($padref==$id){
				$err_padre_ricorsivo=true;
				$errori++;
			}
		}
		if($fontef=="N/D"){
			$err_fonte=true;
			$errori++;
		}
		if($errori>0){
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
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
			if($err_padre_ricorsivo){
echo<<<END

					<li>Padre: NON E' POSSIBILE INDICARE IL REQUISITO STESSO COME PADRE</li>
END;
			}
			if($err_tipo){
echo<<<END

					<li>Tipo: NON INDICATO</li>
END;
			}
			elseif($err_tipo_padre){
echo<<<END

					<li>Tipo: NON COINCIDE CON QUELLO DEL REQUISITO PADRE</li>
END;
			}
			if($err_importanza){
echo<<<END

					<li>Importanza: NON INDICATA</li>
END;
			}
			elseif($err_importanza_padre){
echo<<<END

					<li>Importanza: PIU' STRINGENTE DI QUELLA DEL REQUISITO PADRE</li>
END;
			}
			if($err_stato){
echo<<<END

					<li>Stato: NON INDICATO</li>
END;
			}
			if($err_fonte){
echo<<<END

					<li>Fonte: NON INDICATA</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Requisiti/modificarequisito.php?id=$id">Riprova</a>.</p>
END;
		}
		else{
			$secondari_modificati=1;
			if(($descf==$old_descf) && ($statof==$old_statof) && ($soddisfattof==$old_soddisfattof) && ($implementatof==$old_implementatof) && ($fontef==$old_fontef)){
				$secondari_modificati=0;
			}
			$descf=mysql_escape_string($descf);
			$conn=sql_conn();
			$timestamp_query="SELECT r.Time
							  FROM ReqTracking r
							  WHERE r.CodAuto='$id' AND r.IdTrack=findLastReqTracking('$id')";
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
				<p>Il requisito è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Requisiti/modificarequisito.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL modifyRequisito('$id','$descf',";
					if($tipof==$old_tipof){
						$query=$query."null,";
					}
					else{
						$query=$query."$tipof,";
					}
					if($importanzaf==$old_importanzaf){
						$query=$query."null,";
					}
					else{
						$query=$query."$importanzaf,";
					}
					if($padref==$old_padref){
						$query=$query."0,";
					}
					else{
						if($padref=="N/D"){
							$query=$query."null,";
						}
						else{
							$query=$query."'$padref',";
						}
					}
					$query=$query."'$statof','$implementatof','$soddisfattof','$fontef','$_SESSION[user]','$secondari_modificati',";
					if($modifica_uc==false){
						$query=$query."null)";
					}
					else{
						$query=$query."'$ucf')";
					}
					$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
					$title="Requisito Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il requisito è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Torna a Requisiti</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>Il requisito è stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Torna a Requisiti</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT r.CodAuto,r.IdRequisito,r.Descrizione,r.Tipo+0,r.Importanza+0,r.Padre,r.Stato,r.Soddisfatto,r.Implementato,r.Fonte
				FROM Requisiti r
				WHERE r.CodAuto='$id'";
		$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$requidb=mysql_fetch_row($req);
		if($requidb[0]==$id){
			$title="Modifica Requisito - $requidb[1]";
			startpage_builder($title);
			$tipi=array('Funzionale','Vincolo','Qualita','Prestazionale');
echo<<<END

			<div id="content">
				<h2>Modifica - $requidb[1]</h2>
				<div id="form">
					<form action="$absurl/Requisiti/modificarequisito.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$requidb[2]</textarea>
							</p>
							<p>
								<label for="tipo1">Tipo*:</label>
END;
			if($requidb[3]=="1"){
echo<<<END

								<input type="radio" id="tipo1" name="tipo" value="1" checked="checked" /> Funzionale
END;
			}
			else{
echo<<<END

								<input type="radio" id="tipo1" name="tipo" value="1" /> Funzionale
END;
			}
			if($requidb[3]=="2"){
echo<<<END

								<input type="radio" id="tipo2" name="tipo" value="2" checked="checked" /> Vincolo
END;
			}
			else{
echo<<<END

								<input type="radio" id="tipo2" name="tipo" value="2" /> Vincolo
END;
			}
			if($requidb[3]=="3"){
echo<<<END

								<input type="radio" id="tipo3" name="tipo" value="3" checked="checked" /> Qualità
END;
			}
			else{
echo<<<END

								<input type="radio" id="tipo3" name="tipo" value="3" /> Qualità
END;
			}
			if($requidb[3]=="4"){
echo<<<END
			
								<input type="radio" id="tipo4" name="tipo" value="4" checked="checked" /> Prestazionale
END;
			}
			else{
echo<<<END
			
								<input type="radio" id="tipo4" name="tipo" value="4" /> Prestazionale
END;
			}
echo<<<END

							</p>
							<p>
								<label for="importanza1">Importanza*:</label>
END;
			if($requidb[4]=="1"){
echo<<<END

								<input type="radio" id="importanza1" name="importanza" value="1" checked="checked" /> Obbligatorio
END;
			}
			else{
echo<<<END

								<input type="radio" id="importanza1" name="importanza" value="1" /> Obbligatorio
END;
			}
			if($requidb[4]=="2"){
echo<<<END

								<input type="radio" id="importanza2" name="importanza" value="2" checked="checked" /> Desiderabile
END;
			}
			else{
echo<<<END

								<input type="radio" id="importanza2" name="importanza" value="2" /> Desiderabile
END;
			}
			if($requidb[4]=="3"){
echo<<<END
			
								<input type="radio" id="importanza3" name="importanza" value="3" checked="checked" /> Facoltativo
END;
			}
			else{
echo<<<END
			
								<input type="radio" id="importanza3" name="importanza" value="3" /> Facoltativo
END;
			}
echo<<<END

							</p>
							<p>
								<label for="padre">Padre:</label>
								<select id="padre" name="padre">
									<option value="N/D">N/D</option>
END;
			//$query_ord="CALL sortForest('Requisiti')";
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			foreach($tipi as $tipo){
echo<<<END

									<optgroup label="$tipo" class="first-opt">
END;
				$requi=extract_IdRequisiti($tipo);
				while($row=mysql_fetch_row($requi)){
					if($row[0]!=null){
						if($row[0]==$requidb[5]){
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

									</optgroup>
END;
			}
echo<<<END

								</select>
							</p>
							<p class="mancante">Lo stato deve essere settato solo per requisiti desiderabili e facoltativi</p>
							<p>
								<label for="stato">Stato*:</label>
END;
		if($requidb[4]=="1"){
echo<<<END

								<input type="radio" id="stato1" name="stato" value="0" /> Non Accettato
								<input type="radio" id="stato2" name="stato" value="1" /> Accettato
END;
		}
		elseif($requidb[6]=="0"){
echo<<<END

								<input type="radio" id="stato1" name="stato" value="0" checked="checked" /> Non Accettato
								<input type="radio" id="stato2" name="stato" value="1" /> Accettato
END;
		}
		else{
echo<<<END

								<input type="radio" id="stato1" name="stato" value="0" /> Non Accettato
								<input type="radio" id="stato2" name="stato" value="1" checked="checked" /> Accettato
END;
		}
echo<<<END
	
							</p>
							<p>
								<label for="soddisfatto">Soddisfatto*:</label>
END;
		if($requidb[7]=="0"){
echo<<<END

								<input type="radio" id="soddisfatto1" name="soddisfatto" value="0" checked="checked" /> Non Soddisfatto
								<input type="radio" id="soddisfatto2" name="soddisfatto" value="1" /> Soddisfatto
END;
		}
		else{
echo<<<END

								<input type="radio" id="soddisfatto1" name="soddisfatto" value="0" /> Non Soddisfatto
								<input type="radio" id="soddisfatto2" name="soddisfatto" value="1" checked="checked" /> Soddisfatto
END;
		}
echo<<<END
	
							</p>
							<p>
								<label for="implementato">Implementato*:</label>
END;
		if($requidb[8]=="0"){
echo<<<END

								<input type="radio" id="implementato1" name="implementato" value="0" checked="checked" /> Non Implementato
								<input type="radio" id="implementato2" name="implementato" value="1" /> Implementato
END;
		}
		else{
echo<<<END

								<input type="radio" id="implementato1" name="implementato" value="0" /> Non Implementato
								<input type="radio" id="implementato2" name="implementato" value="1" checked="checked" /> Implementato
END;
		}
echo<<<END

							</p>
							<p>
								<label for="fonte">Fonte*:</label>
								<select id="fonte" name="fonte">
									<option value="N/D">N/D</option>
END;
		$conn=sql_conn();
		$query="SELECT f.CodAuto,f.IdFonte,f.Nome
				FROM Fonti f
				ORDER BY f.IdFonte;";
		$fonti=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($fonti)){
			if($row[0]!=null){
				if($row[0]==$requidb[9]){
echo<<<END

									<option value="$row[0]" selected="selected">$row[1] - $row[2]</option>
END;
				}
				else{
echo<<<END

									<option value="$row[0]">$row[1] - $row[2]</option>
END;
				}
			}
		}
echo<<<END

								</select>
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="ucs">
								<label for="uc1">Use Case Correlati:</label>
END;
		//$query_ord="CALL sortForest('UseCase')";
		$query="SELECT u.CodAuto,u.IdUC
				FROM _MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$tutti_uc_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$tutti_uc=array();
		while($row=mysql_fetch_row($tutti_uc_query)){
			$tutti_uc["$row[1]"]=$row[0];
		}
		$query="SELECT u.CodAuto,u.IdUC
				FROM RequisitiUC ruc JOIN (_MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto) ON ruc.UC=u.CodAuto
				WHERE ruc.CodReq='$id'
				ORDER BY h.Position";
		$uc_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$uc=array();
		$listaolduc="";
		$i=0;
		while($row=mysql_fetch_row($uc_query)){
			$uc["$row[1]"]=$row[0];
			$listaolduc=($listaolduc.$row[0]).",";
		}
		$uc_rimanenti=array();
		foreach($tutti_uc as $tiid => $tcod){
			$trovato=false;
			foreach($uc as $conf_iid => $conf_cod){
				if($conf_cod==$tcod){
					$trovato=true;
				}
			}
			if($trovato==false){
				$uc_rimanenti["$tiid"]=$tcod;
			}
		}
		foreach($uc as $iid => $cod){
			$i++;
echo<<<END

								<select id="uc$i" name="uc$i" onchange="multiple_sel(3,$i)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
			foreach($uc_rimanenti as $riid => $rcod){
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

								<select id="uc$i" name="uc$i" onchange="multiple_sel(3,$i)">
									<option value="N/D">N/D</option>
END;
		foreach($uc_rimanenti as $riid => $rcod){
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

							<input type="hidden" id="num_uc" name="num_uc" value="$i" />
							<input type="hidden" id="old_desc" name="old_desc" value="$requidb[2]" />
							<input type="hidden" id="old_tipo" name="old_tipo" value="$requidb[3]" />
							<input type="hidden" id="old_importanza" name="old_importanza" value="$requidb[4]" />
							<input type="hidden" id="old_padre" name="old_padre" value="$requidb[5]" />
							<input type="hidden" id="old_stato" name="old_stato" value="$requidb[6]" />
							<input type="hidden" id="old_soddisfatto" name="old_soddisfatto" value="$requidb[7]" />
							<input type="hidden" id="old_implementato" name="old_implementato" value="$requidb[8]" />
							<input type="hidden" id="old_fonte" name="old_fonte" value="$requidb[9]" />
							<input type="hidden" id="lista_old_uc" name="lista_old_uc" value="$listaolduc" />
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
			$title="Modifica Requisito - Requisito Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il requisito con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Torna a Requisiti</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>