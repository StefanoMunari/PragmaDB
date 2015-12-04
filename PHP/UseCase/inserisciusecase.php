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
		$num_requif=$_POST["num_requi"];
		$num_attoref=$_POST["num_attore"];
		$err_nome=false;
		$err_desc=false;
		$err_pre=false;
		$err_post=false;
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
				<p><a class="link-color-pers" href="$absurl/UseCase/inserisciusecase.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
			for($i=1;$i<=$num_attoref;$i++){
				$temp=$_POST["attore$i"];
				$attorif="$attorif"."$temp".",";
			}
			for($i=1;$i<=$num_requif;$i++){
				$temp=$_POST["requi$i"];
				$requif="$requif"."$temp".",";
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
			$query="CALL insertUseCase('$nomef',";
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
			$query=$query."'$requif','$attorif','$_SESSION[user]')";
			$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$title="Use Case Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Lo use case è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/UseCase/usecase.php">Torna a UseCase</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Inserisci Use Case";
		startpage_builder($title);
		$tipi=array('Funzionale','Vincolo','Qualita','Prestazionale');
echo<<<END

			<div id="content">
				<h2>Inserisci Use Case</h2>
				<div id="form">
					<form action="$absurl/UseCase/inserisciusecase.php" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="300" />
							</p>
							<p>
								<label for="nome">Diagramma:</label>
								<input type="text" id="diag" name="diag" maxlength="50" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="pre">Precondizioni*:</label>
								<textarea rows="2" cols="0" id="pre" name="pre" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="post">Postcondizioni*:</label>
								<textarea rows="2" cols="0" id="post" name="post" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="padre">Padre:</label>
								<select id="padre" name="padre">
									<option value="N/D">N/D</option>
END;
		$conn=sql_conn();
		//$query_ord="CALL sortForest('UseCase')";
		$query="SELECT u.CodAuto, u.IdUC
				FROM _MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($uc)){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
		}
echo<<<END

								</select>
							</p>
							<p>
								<label for="princ">Scenario Principale*:</label>
								<textarea rows="2" cols="0" id="princ" name="princ" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="inclusioni">Inclusioni:</label>
								<textarea rows="2" cols="0" id="inclusioni" name="inclusioni" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="estensioni">Estensioni:</label>
								<textarea rows="2" cols="0" id="estensioni" name="estensioni" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="alte">Scenari Alternativi:</label>
								<textarea rows="2" cols="0" id="alte" name="alte" maxlength="10000"></textarea>
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="attores">
								<label for="attore1">Attori Coinvolti:</label>
								<select id="attore1" name="attore1" onchange="multiple_sel(1,1)">
									<option value="N/D">N/D</option>
END;
		$conn=sql_conn();
		$query="SELECT a.CodAuto, a.Nome
				FROM Attori a
				ORDER BY a.Nome";
		$attori=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($attori)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
							<p id="requis">
								<label for="requi1">Requisiti Correlati:</label>
								<select id="requi1" name="requi1" onchange="multiple_sel(2,1)">
									<option value="N/D">N/D</option>
END;
		$conn=sql_conn();
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto, r.IdRequisito
				FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$requi=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($requi)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
							<input type="hidden" id="num_requi" name="num_requi" value="0" />
							<input type="hidden" id="num_attore" name="num_attore" value="0" />
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