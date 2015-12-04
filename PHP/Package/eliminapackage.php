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
		header("Location: $absurl/Package/package.php");
	}
	elseif(isset($_REQUEST['yes'])){
		$id=$_GET['id'];
		$timestampf=$_POST["timestamp"];
		$conn=sql_conn();
		$timestamp_query="SELECT p.Time
							FROM Package p
							WHERE p.CodAuto='$id'"; //Query che recupera il timestamp dell'utlima modifica al db di $id
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
				<p>Il package è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Package/eliminapackage.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
			</div>
END;
			}
			else{
				$query="CALL removePackage('$id')"; //Chiama la SP per la rimozione
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Package Eliminato";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il package è stato eliminato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Package/package.php">Torna a Package</a>.</p>
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
				<p>Il package è già stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Package/package.php">Torna a Package</a>.</p>
			</div>
END;
		}
	} //Fine caso in cui elimino
	else{
		//L'utente non ha ancora scelto se eliminare o meno, gli stampo quello che sta cercando di eliminare e un form per scegliere
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT p1.CodAuto,p1.PrefixNome,p1.Nome,p1.Descrizione,p2.PrefixNome,p1.UML,p2.CodAuto
				FROM Package p1 LEFT JOIN Package p2 ON p1.Padre=p2.CodAuto
				WHERE p1.CodAuto='$id'"; //Query per recuperare il Package
		$pack=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($pack);
		if($row[0]==$id){
			$title="Elimina Package - $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Elimina - $row[1]</h2>
				<p>Sei sicuro di voler eliminare il seguente package?</p>
				<table>
					<thead>
						<tr>
							<th>PrefixNome</th>
							<th>Nome</th>
							<th>Descrizione</th>
							<th>Padre</th>
							<th>Diagramma</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			package_table($row);
echo<<<END

						</tr>
					</tbody>
				</table>
				<div id="form">
					<form action="$absurl/Package/eliminapackage.php?id=$id" method="post">
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
			$title="Elimina Package - Package Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il package con id "$id" non è presente nel database.</p>
			</div>
END;
		}
	}
	endpage_builder();
}
?>