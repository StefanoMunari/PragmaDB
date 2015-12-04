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
		//Ho dei dati da inserire
		$nomef=$_POST["nome"]; //nome del package ricevuto dal form
		$descf=$_POST["desc"]; //descrizione del package
		$padref=$_POST["padre"]; //padre del package
		$prefixPadre="";
		$diagf=$_POST["diag"]; //Percorso del diagramma uml
		$pkgf=""; //Package correlati
		$num_pkgf=$_POST["num_pkg"]; //Numero di requisiti soddisfatti dal package
		/*$requif=""; //Requisiti soddisfatti dal package
		$num_requif=$_POST["num_requi"]; //Numero di requisiti soddisfatti dal package*/
		$err_nome=false;
		$err_desc=false;
		$err_padre=false;
		$err_presente=false;
		$errori=0;
		if($nomef==null){
			$err_nome=true;
			$errori++;
		}
		if($descf==null){
			$err_desc=true;
			$errori++;
		}
		if($padref!="N/D"){
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
		}
		$nomef=mysql_escape_string($nomef);
		$descf=mysql_escape_string($descf);
		$diagf=mysql_escape_string($diagf);
		$conn=sql_conn();
		$query="SELECT COUNT(*)
				FROM Package p
				WHERE p.PrefixNome='$prefixPadre"."$nomef'";
		$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$row=mysql_fetch_row($ris);
		if($row[0]>0){
			$err_presente=true;
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
			if($err_padre){
echo<<<END

					<li>Padre: IL PADRE INDICATO NON ESISTE!</li>
END;
			}
			if($err_presente){
echo<<<END

					<li>IL PACKAGE E' GIA' PRESENTE NEL DB</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Package/inseriscipackage.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
			//Parsa i package correlati
			for($i=1;$i<=$num_pkgf;$i++){
				$temp=$_POST["pkg$i"];
				$pkgf="$pkgf"."$temp".",";
			}
			//Parsa i requisiti correlati
			/*for($i=1;$i<=$num_requif;$i++){
				$temp=$_POST["requi$i"];
				$requif="$requif"."$temp".",";
			}*/
			$conn=sql_conn();
			$query1="CALL insertPackage('$nomef','$prefixPadre"."$nomef','$descf',";
			if($diagf==null){
				$query1=$query1."null,";
			}
			else{
				$query1=$query1."'$diagf',";
			}
			if($padref=="N/D"){
				$query1=$query1."null,";
			}
			else{
				$query1=$query1."'$padref',";
			}			
			$query1=$query1."'P')";
			$query1=mysql_query($query1,$conn) or fail("Query fallita: Inserimento Package Fallito - ".mysql_error($conn));
			$queryCod="SELECT p.CodAuto
						FROM Package p
						WHERE p.PrefixNome='$prefixPadre"."$nomef'";
			$queryCod=mysql_query($queryCod,$conn) or fail("Query fallita: Package non trovato nel DB - ".mysql_error($conn));
			$row=mysql_fetch_row($queryCod);
			if($row[0]!=null){
				$cod=$row[0];
			}
			else{
				fail("Query fallita: Package non trovato nel DB");
			}
			if($num_pkgf>0){
				$query2="CALL insertRelatedPackage('$cod','$pkgf')";
				$query2=mysql_query($query2,$conn) or fail("Query fallita: Inserimento Package Correlati Fallito - ".mysql_error($conn));
			}
			/*if($num_requif>0){
				$query3="CALL insertPackageRequisiti('$cod','$requif')";
				$query3=mysql_query($query3,$conn) or fail("Query fallita: Inserimento Requisiti Correlati Fallito - ".mysql_error($conn));
			}*/
			$title="Package Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il package è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Package/package.php">Torna a Package</a>.</p>
			</div>
END;
		}
	}
	else{
		//Non ho ricevuto nessun dato in post
		//Mostro il form per l'inserimento
		$title="Inserisci Package";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Inserisci Package</h2>
				<div id="form">
					<form action="$absurl/Package/inseriscipackage.php" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="100" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="padre">Padre:</label>
								<select id="padre" name="padre">
									<option value="N/D">N/D</option>
END;
		$conn=sql_conn();
		$query="SELECT p.CodAuto,p.PrefixNome
				FROM Package p
				ORDER BY p.PrefixNome"; //Query per recuperare l'id di tutti i package
					//in modo che $row[0] sia l'id e che $row[1] sia il [prefisso::]nome 
		$father=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($father)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
							<p>
								<label for="diag">Diagramma:</label>
								<input type="text" id="diag" name="diag" maxlength="50" />
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="pkgs">
								<label for="pkg1">Componenti Correlati:</label>
								<select id="pkg1" name="pkg1" onchange="multiple_sel(4,1)">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista dei package disponibili
		$conn=sql_conn();
		$query="SELECT p.CodAuto, p.PrefixNome
				FROM Package p
				ORDER BY p.PrefixNome"; //Query che calcola i requisiti disponibili
		$related=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($related)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
END;
/*							<p id="requis">
								<label for="requi1">Requisiti Correlati:</label>
								<select id="requi1" name="requi1" onchange="multiple_sel(2,1)">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista dei requisiti disponibili
		$conn=sql_conn();
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto, r.IdRequisito
				FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
				ORDER BY h.Position"; //Query che calcola i requisiti disponibili
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
							</p>*/
echo<<<END

							<input type="hidden" id="num_pkg" name="num_pkg" value="0" />
							<input type="hidden" id="num_requi" name="num_requi" value="0" />
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