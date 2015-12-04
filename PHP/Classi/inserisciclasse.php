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
		$nomef=$_POST["nome"]; //nome della classe ricevuta dal form
		$descf=$_POST["desc"]; //descrizione della classe
		$utilf=$_POST["util"]; //Utilizzo della classe
		$padref=$_POST["padre"]; //package padre della classe
		$prefixPadre="";
		$diagf=$_POST["diag"]; //Percorso del diagramma uml
		$cluf=""; //Eredita da correlati
		$num_cluf=$_POST["num_clu"]; //Numero di classi Eredita Da
		$cldf=""; //Ereditano da lei correlati
		$num_cldf=$_POST["num_cld"]; //Numero di classi Ereditano da lei
		$cltf=""; //Classi IN
		$num_cltf=$_POST["num_clt"]; //Numero di Classi IN
		$clqf=""; //Classi OUT
		$num_clqf=$_POST["num_clq"]; //Numero di Classi OUT
		$requif=""; //Requisiti Correlati
		$num_requif=$_POST["num_requi"]; //Numero di Requisiti Correlati
		$err_nome=false;
		$err_desc=false;
		$err_padre=false;
		$err_contenitore_padre=false;
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
				$err_contenitore_padre=true;
				$errori++;
			}
			else{
				$prefixPadre="$row[1]::";
			}
		}
		else{
			$err_padre=true;
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

					<li>ContenutaIn: PACKAGE CONTENITORE NON INDICATO</li>
END;
			}
			if($err_contenitore_padre){
echo<<<END

					<li>ContenutaIn: IL PACKAGE INDICATO NON ESISTE</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Classi/inserisciclasse.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
			//Parsa gli Eredita Da
			for($i=1;$i<=$num_cluf;$i++){
				$temp=$_POST["clu$i"];
				$cluf="$cluf"."$temp".",";
			}
			//Parsa gli Ereditano da lei
			for($i=1;$i<=$num_cldf;$i++){
				$temp=$_POST["cld$i"];
				$cldf="$cldf"."$temp".",";
			}
			//Parsa gli IN
			for($i=1;$i<=$num_cltf;$i++){
				$temp=$_POST["clt$i"];
				$cltf="$cltf"."$temp".",";
			}
			//Parsa gli OUT
			for($i=1;$i<=$num_clqf;$i++){
				$temp=$_POST["clq$i"];
				$clqf="$clqf"."$temp".",";
			}
			//Parsa i Requisiti Correlati
			for($i=1;$i<=$num_requif;$i++){
				$temp=$_POST["requi$i"];
				$requif="$requif"."$temp".",";
			}
			$nomef=mysql_escape_string($nomef);
			$descf=mysql_escape_string($descf);
			$utilf=mysql_escape_string($utilf);
			$diagf=mysql_escape_string($diagf);
			$conn=sql_conn();
			$query1="CALL insertClasse('$nomef','$descf','$prefixPadre"."$nomef',";
			if($utilf==null){
				$query1=$query1."null,";
			}
			else{
				$query1=$query1."'$utilf',";
			}
			$query1=$query1."'$padref',";
			if($diagf==null){
				$query1=$query1."null)";
			}
			else{
				$query1=$query1."'$diagf')";
			}
			$query1=mysql_query($query1,$conn) or fail("Query fallita: Inserimento Classe Fallito - ".mysql_error($conn));
			$queryCod="SELECT c.CodAuto
						FROM Classe c
						WHERE c.PrefixNome='$prefixPadre"."$nomef'";
			$queryCod=mysql_query($queryCod,$conn) or fail("Query fallita: Classe non trovata nel DB - ".mysql_error($conn));
			$row=mysql_fetch_row($queryCod);
			if($row[0]!=null){
				$cod=$row[0];
			}
			else{
				fail("Query fallita: Classe non trovata nel DB");
			}
			if($num_cluf>0){
				$query2="CALL insertEreditaDa('$cod','$cluf')";
				$query2=mysql_query($query2,$conn) or fail("Query fallita: Inserimento Eredita Da Fallito - ".mysql_error($conn));
			}
			if($num_cldf>0){
				$query3="CALL insertEreditataDa('$cod','$cldf')";
				$query3=mysql_query($query3,$conn) or fail("Query fallita: Inserimento Ereditano Da Lei Fallito - ".mysql_error($conn));
			}
			if($num_cltf>0){
				$query4="CALL insertRelazioneA('$cod','$cltf')";
				$query4=mysql_query($query4,$conn) or fail("Query fallita: Inserimento IN Fallito - ".mysql_error($conn));
			}
			if($num_clqf>0){
				$query5="CALL insertRelazioneDa('$cod','$clqf')";
				$query5=mysql_query($query5,$conn) or fail("Query fallita: Inserimento OUT Fallito - ".mysql_error($conn));
			}
			if($num_requif>0){
				$query6="CALL insertClasseRequisiti('$cod','$requif')";
				$query6=mysql_query($query6,$conn) or fail("Query fallita: Inserimento Requisiti Correlati Fallito - ".mysql_error($conn));
			}
			$title="Classe Inserita";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>La classe è stata inserita con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
			</div>
END;
		}
	}
	else{
		//Non ho ricevuto nessun dato in post
		//Mostro il form per l'inserimento
		$title="Inserisci Classe";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Inserisci Classe</h2>
				<div id="form">
					<form action="$absurl/Classi/inserisciclasse.php" method="post">
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
								<label for="util">Utilizzo:</label>
								<textarea rows="2" cols="0" id="util" name="util" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="padre">ContenutaIn*:</label>
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
							<p id="clus">
								<label for="clu1">Eredita Da:</label>
								<select id="clu1" name="clu1" onchange="multiple_sel(5,1)">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista delle classi disponibili
		$conn=sql_conn();
		$query="SELECT c.CodAuto, c.PrefixNome
				FROM Classe c
				ORDER BY c.PrefixNome"; //Query che calcola le classi disponibili
		$in=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($in)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
							<p id="clds">
								<label for="cld1">Ereditano da lei:</label>
								<select id="cld1" name="cld1" onchange="multiple_sel(6,1)">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista delle classi disponibili
		$conn=sql_conn();
		$query="SELECT c.CodAuto, c.PrefixNome
				FROM Classe c
				ORDER BY c.PrefixNome"; //Query che calcola le classi disponibili
		$out=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($out)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END
								</select>
							</p>
							<p id="clts">
								<label for="clt1">Classi Correlate - IN:</label>
								<select id="clt1" name="clt1" onchange="multiple_sel(7,1)">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista delle classi disponibili
		$conn=sql_conn();
		$query="SELECT c.CodAuto, c.PrefixNome
				FROM Classe c
				ORDER BY c.PrefixNome"; //Query che calcola le classi disponibili
		$in=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($in)){
			if($row[0]!=null){
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
			}
		}
echo<<<END

								</select>
							</p>
							<p id="clqs">
								<label for="clq1">Classi Correlate - OUT:</label>
								<select id="clq1" name="clq1" onchange="multiple_sel(8,1)">
									<option value="N/D">N/D</option>
END;
		//Stampo la lista delle classi disponibili
		$conn=sql_conn();
		$query="SELECT c.CodAuto, c.PrefixNome
				FROM Classe c
				ORDER BY c.PrefixNome"; //Query che calcola le classi disponibili
		$out=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($row=mysql_fetch_row($out)){
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
							</p>
							<input type="hidden" id="num_clu" name="num_clu" value="0" />
							<input type="hidden" id="num_cld" name="num_cld" value="0" />
							<input type="hidden" id="num_clt" name="num_clt" value="0" />
							<input type="hidden" id="num_clq" name="num_clq" value="0" />
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