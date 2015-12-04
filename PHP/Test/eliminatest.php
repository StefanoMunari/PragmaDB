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
	if(isset($_REQUEST['no'])){
		header("Location: $absurl/Test/test.php");
	}
	elseif(isset($_REQUEST['yes'])){
		$id=$_GET['id'];
		$timestampf=$_POST["timestamp"];
		$conn=sql_conn();
		$timestamp_query="SELECT t.Time
						  FROM Test t
						  WHERE t.CodAuto='$id'"; //Query che recupera il timestamp dell'utlima modifica al db di $id
		$timestamp_query=mysql_query($timestamp_query,$conn) or fail("Query fallita: ".mysql_error($conn));
		if($row=mysql_fetch_row($timestamp_query)){
			$timestamp_db=$row[0];
			$timestamp_db=strtotime($timestamp_db);
			if($timestampf<$timestamp_db){
				//Il timestamp del form è < di quello del DB. blocco le modifiche potrei fare disastri
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell' eliminazione:</h2>
				<p>Il test è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Test/eliminatest.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
			</div>
END;
			}
			else{
				$query="CALL removeTest('$id')"; //Chiama la SP per la rimozione
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Test Eliminato";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il test è stato eliminato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Test/test.php">Torna a Test</a>.</p>
			</div>
END;
			}
		}
		else{
			//Non ho trovato il timestamp dell'utilma modifica, vuol dire che qualcuno ha eliminato l'elemento o che non esiste
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'eliminazione:</h2>
				<p>Il test è già stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Test/test.php">Torna a Test</a>.</p>
			</div>
END;
		}
	} //Fine caso in cui elimino
	else{
		//L'utente non ha ancora scelto se eliminare o meno, gli stampo quello che sta cercando di eliminare e un form per scegliere
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
			$query="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
					FROM Test t JOIN Requisiti r ON t.Requisito=r.CodAuto
					WHERE t.CodAuto='$id'";
		}
		elseif($tipo=="Sistema"){
			$query="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
					FROM Test t JOIN Requisiti r ON t.Requisito=r.CodAuto
					WHERE t.CodAuto='$id'";
		}
		elseif($tipo=="Integrazione"){
			$query="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, p.PrefixNome, p.CodAuto
					FROM Test t JOIN Package p ON t.Package=p.CodAuto
					WHERE t.CodAuto='$id'";
		}
		else{
			$query="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo
					FROM Test t
					WHERE t.CodAuto='$id'";
		}
		$test=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($test);
		if($row[0]==$id){
			$title="Elimina Test - $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Elimina - $row[1]</h2>
				<p>Sei sicuro di voler eliminare il seguente test?</p>
				<table>
					<thead>
						<tr>
							<th>IdTest</th>
							<th>Descrizione</th>
							<th>Implementato</th>
							<th>Eseguito</th>
							<th>Esito</th>
							<th>Oggetto</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			test_table($row);
echo<<<END

						</tr>
					</tbody>
				</table>
				<div id="form">
					<form action="$absurl/Test/eliminatest.php?id=$id" method="post">
						<fieldset>
							<input type="hidden" id="timestamp" name="timestamp" value="$timestamp" />
							<p>
								<input type="submit" id="yes" name="yes" value="Elimina" />
								<input type="submit" id="no" name="no" value="Annulla" />
							</p>
						</fieldset>
					</form>
				</div>
			</div>
END;
		}
		else{
			//Non ho trovato quello che l'utente vuole eliminare
			$title="Elimina Test - Test Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il test con id "$id" non è presente nel database.</p>
			</div>
END;
		}
	}
	endpage_builder();
}
?>