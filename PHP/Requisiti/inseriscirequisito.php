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
		$descf=$_POST["desc"];
		if(isset($_POST["tipo"])){
			$tipof=$_POST["tipo"];
		}
		if(isset($_POST["importanza"])){
			$importanzaf=$_POST["importanza"];
		}
		$padref=$_POST["padre"];
		if(isset($_POST["stato"])){
			$statof=$_POST["stato"];
		}
		$soddisfattof=$_POST["soddisfatto"];
		$implementatof=$_POST["implementato"];
		$fontef=$_POST["fonte"];
		$ucf="";
		$num_ucf=$_POST["num_uc"];
		$err_desc=false;
		$err_tipo=false;
		$err_importanza=false;
		$err_tipo_padre=false;
		$err_importanza_padre=false;
		$err_stato=false;
		$err_fonte=false;
		$errori=0;
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
				<h2>Errore nell'inserimento dei seguenti campi:</h2>
				<ul>
END;
			if($err_desc){
echo<<<END

					<li>Descrizione: NON INSERITA</li>
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
				<p><a class="link-color-pers" href="$absurl/Requisiti/inseriscirequisito.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
			for($i=1;$i<=$num_ucf;$i++){
				$temp=$_POST["uc$i"];
				$ucf="$ucf"."$temp".",";
			}
			$descf=mysql_escape_string($descf);
			$conn=sql_conn();
			if($padref=="N/D"){
				$query="CALL insertRequisito('$_SESSION[user]','$descf',$tipof,$importanzaf,null,'$statof','$implementatof','$soddisfattof','$fontef','$ucf')";
			}
			else{
				$query="CALL insertRequisito('$_SESSION[user]','$descf',$tipof,$importanzaf,'$padref','$statof','$implementatof','$soddisfattof','$fontef','$ucf')";
			}
			$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$title="Requisito Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il requisito è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Torna a Requisiti</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Inserisci Requisito";
		startpage_builder($title);
		$tipi=array('Funzionale','Vincolo','Qualita','Prestazionale');
echo<<<END

			<div id="content">
				<h2>Inserisci Requisito</h2>
				<div id="form">
					<form action="$absurl/Requisiti/inseriscirequisito.php" method="post">
						<fieldset>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="tipo1">Tipo*:</label>
								<input type="radio" id="tipo1" name="tipo" value="1" /> Funzionale
								<input type="radio" id="tipo2" name="tipo" value="2" /> Vincolo
								<input type="radio" id="tipo3" name="tipo" value="3" /> Qualità
								<input type="radio" id="tipo4" name="tipo" value="4" /> Prestazionale
							</p>
							<p>
								<label for="importanza1">Importanza*:</label>
								<input type="radio" id="importanza1" name="importanza" value="1" /> Obbligatorio
								<input type="radio" id="importanza2" name="importanza" value="2" /> Desiderabile
								<input type="radio" id="importanza3" name="importanza" value="3" /> Facoltativo
							</p>
							<p>
								<label for="padre">Padre:</label>
								<select id="padre" name="padre">
									<option value="N/D">N/D</option>
END;
		$conn=sql_conn();
		//$query_ord="CALL sortForest('Requisiti')";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		foreach($tipi as $tipo){
echo<<<END

									<optgroup label="$tipo" class="first-opt">
END;
			$requi=extract_IdRequisiti($tipo);
			while($row=mysql_fetch_row($requi)){
				if($row[0]!=null){
echo<<<END

										<option value="$row[0]">$row[1]</option>
END;
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
								<input type="radio" id="stato1" name="stato" value="0" /> Non Accettato
								<input type="radio" id="stato2" name="stato" value="1" /> Accettato
							</p>
							<p>
								<label for="soddisfatto">Soddisfatto*:</label>
								<input type="radio" id="soddisfatto1" name="soddisfatto" value="0" checked="checked" /> Non Soddisfatto
								<input type="radio" id="soddisfatto2" name="soddisfatto" value="1" /> Soddisfatto
							</p>
							<p>
								<label for="implementato">Implementato*:</label>
								<input type="radio" id="implementato1" name="implementato" value="0" checked="checked" /> Non Implementato
								<input type="radio" id="implementato2" name="implementato" value="1" /> Implementato
							</p>
							<p>
								<label for="fonte">Fonte*:</label>
								<select id="fonte" name="fonte">
									<option value="N/D">N/D</option>
END;
		$query="SELECT f.CodAuto,f.IdFonte,f.Nome
				FROM Fonti f
				ORDER BY f.IdFonte;";
		$fonti=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($fonti)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1] - $row[2]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="ucs">
								<label for="uc1">Use Case Correlati:</label>
								<select id="uc1" name="uc1" onchange="multiple_sel(3,1)">
									<option value="N/D">N/D</option>
END;
		$conn=sql_conn();
		//$query_ord="CALL sortForest('UseCase')";
		$query="SELECT u.CodAuto,u.IdUC
				FROM _MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($uc)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
							<input type="hidden" id="num_uc" name="num_uc" value="0" />
							<p>
								<input type="submit" id="submit" name="submit" value="Inserisci" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
			</div>
END;
	}
	endpage_builder();
}
?>