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
		if(isset($_POST["tipo"])){
			$tipof=$_POST["tipo"]; //tipo del test
		}
		$descf=$_POST["desc"]; //descrizione del package
		$implementatof=$_POST["implementato"]; //test implementato o meno
		$eseguitof=$_POST["eseguito"]; //test eseguito o meno
		$esitof=$_POST["esito"]; //test superato o meno
		$requi1f=$_POST["requi1"]; //Requisito Correlato (Validazione)
		$requi2f=$_POST["requi2"]; //Requisito Correlato (Sistema)
		$pkgf=$_POST["pkg"];; //Package Correlato (Integrazione)
		$metf=""; //Metodi Correlati (Unita)
		$num_metf=$_POST["num_met"]; //Numero di metodi correlati
		$err_tipo=false;
		$err_desc=false;
		$err_requi=false;
		$err_requi_doppio=false;
		$err_pkg=false;
		$err_pkg_doppio=false;
		$err_met=false;
		$errori=0;
		$conn=sql_conn();
		if(!(isset($tipof))){
			$err_tipo=true;
			$errori++;
		}
		else{
			if($tipof=="Validazione"){
				if($requi1f=="N/D"){
					$err_requi=true;
					$errori++;
				}
				else{
					$query="SELECT COUNT(*)
							FROM Test t
							WHERE t.tipo='$tipof' AND t.Requisito='$requi1f'";
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
							WHERE t.tipo='$tipof' AND t.Requisito='$requi2f'";
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
							WHERE t.Package='$pkgf'";
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
			if($err_tipo){
echo<<<END

					<li>Tipo: NON INDICATO</li>
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
				<p><a class="link-color-pers" href="$absurl/Test/inseriscitest.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
			$descf=mysql_escape_string($descf);
			//Parsa i metodi correlati
			for($i=1;$i<=$num_metf;$i++){
				$temp=$_POST["met$i"];
				$metf="$metf"."$temp".",";
			}
			$query1="CALL insertTest('$tipof','$descf','$implementatof','$eseguitof','$esitof',";
			if($tipof=="Validazione"){
				$query1=$query1."'$requi1f',";
			}
			elseif($tipof=="Sistema"){
				$query1=$query1."'$requi2f',";
			}
			else{
				$query1=$query1."null,";
			}
			if($pkgf=="N/D"){
				$query1=$query1."null)";
			}
			else{
				$query1=$query1."'$pkgf')";
			}
			$query1=mysql_query($query1,$conn) or fail("Query fallita: Inserimento Test Fallito - ".mysql_error($conn));
			if($num_metf>0){
				$queryCod="SELECT t.CodAuto
						   FROM Test t
						   WHERE t.Tipo='$tipof' AND t.Descrizione='$descf'
						   ORDER BY t.Time DESC";
				$queryCod=mysql_query($queryCod,$conn) or fail("Query fallita: Test non trovato nel DB - ".mysql_error($conn));
				$row=mysql_fetch_row($queryCod);
				if($row[0]!=null){
					$cod=$row[0];
				}
				else{
					fail("Query fallita: Test non trovato nel DB");
				}
				$query2="CALL insertTestMetodi('$cod','$metf')";
				$query2=mysql_query($query2,$conn) or fail("Query fallita: Inserimento Metodi Correlati Fallito - ".mysql_error($conn));
			}
			$title="Test Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il test è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Test/test.php">Torna a Test</a>.</p>
			</div>
END;
		}
	}
	else{
		//Non ho ricevuto nessun dato in post
		//Mostro il form per l'inserimento
		$title="Inserisci Test";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<script type="text/javascript" src="$absurl/Test/script_test.js"></script>
				<h2>Inserisci Test</h2>
				<div id="form">
					<form action="$absurl/Test/inseriscitest.php" method="post" onsubmit="return validateForm()">
						<fieldset>
							<p>
								<label for="tipo1">Tipo*:</label>
								<input type="radio" id="tipo1" name="tipo" value="Validazione" onchange="val()" /> Validazione
								<input type="radio" id="tipo2" name="tipo" value="Sistema" onchange="sis()" /> Sistema
								<input type="radio" id="tipo3" name="tipo" value="Integrazione" onchange="int()" /> Integrazione
								<input type="radio" id="tipo4" name="tipo" value="Unita" onchange="uni()" /> Unità
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="implementato">Implementato*:</label>
								<input type="radio" id="implementato1" name="implementato" value="0" checked="checked" onchange="notImpl()" /> Non Implementato
								<input type="radio" id="implementato2" name="implementato" value="1" onchange="impl()" /> Implementato
							</p>
							<p id="esegs" style="display:none;">
								<label for="eseguito">Eseguito*:</label>
								<input type="radio" id="eseguito1" name="eseguito" value="0" checked="checked" onchange="notExec()" /> Non Eseguito
								<input type="radio" id="eseguito2" name="eseguito" value="1" onchange="exec()" /> Eseguito
							</p>
							<p id="esits" style="display:none;">
								<label for="esito">Esito*:</label>
								<input type="radio" id="esito1" name="esito" value="0" checked="checked" /> Non Superato
								<input type="radio" id="esito2" name="esito" value="1" /> Superato
							</p>
							<p id="requis1" style="display:none;">
								<label for="requi1">Requisito Correlato*:</label>
								<select id="requi1" name="requi1">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista dei requisiti disponibili
		$conn=sql_conn();
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto, r.IdRequisito
				FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
				WHERE r.CodAuto NOT IN (SELECT t.Requisito FROM Test t WHERE t.Tipo='Validazione' AND t.Requisito IS NOT NULL)
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
							</p>
							<p id="requis2" style="display:none;">
								<label for="requi2">Requisito Correlato*:</label>
								<select id="requi2" name="requi2">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista dei requisiti disponibili
		$conn=sql_conn();
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto, r.IdRequisito
				FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
				WHERE r.CodAuto NOT IN (SELECT t.Requisito FROM Test t WHERE t.Tipo='Sistema' AND t.Requisito IS NOT NULL)
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
							</p>
							<p id="pkgs" style="display:none;">
								<label for="pkg">Componente Correlato*:</label>
								<select id="pkg" name="pkg">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista dei package disponibili
		$query="SELECT p.CodAuto, p.PrefixNome
				FROM Package p
				WHERE p.CodAuto NOT IN (SELECT t.Package FROM Test t WHERE t.Package IS NOT NULL)
				ORDER BY p.PrefixNome"; //Query che calcola i package disponibili
		$pack=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($pack)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END
								</select>
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="mets" style="display:none;">
								<label for="met1">Metodi Correlati*:</label>
								<select id="met0" name="met0" style="display:none;">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista dei metodi disponibili
		$conn=sql_conn();
		$query="SELECT m.CodAuto, CONCAT(c.PrefixNome,'::',m.Nome), m.ReturnType
				FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
				ORDER BY CONCAT(c.PrefixNome,'::',m.Nome)"; //Query che calcola i requisiti disponibili
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
								<select id="met1" name="met1" onchange="multiple_sel(9,1)">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista dei metodi disponibili
		$conn=sql_conn();
		$query="SELECT m.CodAuto, CONCAT(c.PrefixNome,'::',m.Nome), m.ReturnType
				FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
				ORDER BY CONCAT(c.PrefixNome,'::',m.Nome)"; //Query che calcola i requisiti disponibili
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
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
							</p>
							<input type="hidden" id="num_met" name="num_met" value="0" />
							<p>
								<input type="submit" id="submit" name="submit" value="Inserisci" />
								<input type="reset" id="reset" name="reset" value="Cancella" onclick="resetForm()" />
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