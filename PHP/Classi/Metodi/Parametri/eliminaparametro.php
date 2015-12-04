<?php

require('../../../Functions/mysql_fun.php');
require('../../../Functions/page_builder.php');
require('../../../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	if(isset($_REQUEST['no'])){
		$me=$_POST["me"];
		header("Location: $absurl/Classi/Metodi/Parametri/parametri.php?me=$me");
	}
	elseif(isset($_REQUEST['yes'])){
		$id=$_GET['id'];
		$me=$_POST["me"];
		$cl=$_POST["cl"];
		$timestampf=$_POST["timestamp"];
		$conn=sql_conn();
		$timestamp_query="SELECT c.Time
							FROM Classe c
							WHERE c.CodAuto='$cl'"; //Query che recupera il timestamp dell'utlima modifica al db di $id
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
				<p>La classe contenitore è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$me">ottieni i dati aggiornati e riprova</a>.</p>
			</div>
END;
			}
			else{
				$query="CALL removeParametro('$id','$cl')"; //Chiama la SP per la rimozione
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Parametro Eliminato";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il parametro è stato eliminato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$me">Torna a Parametri</a>.</p>
			</div>
END;
			}
		}
		else{
			//Non ho trovato il timestamp dell'ultima modifica, vuol dire che qualcuno ha eliminato l'elemento o che non esiste
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'eliminazione:</h2>
				<p>La classe contenitore è stata eliminata da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
			</div>
END;
		}
	} //Fine caso in cui elimino
	else{
		//L'utente non ha ancora scelto se eliminare o meno, gli stampo quello che sta cercando di eliminare e un form per scegliere
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT p.CodAuto, p.Nome, p.Tipo, p.Descrizione, m.Nome, p.Metodo, c.PrefixNome, c.CodAuto
				FROM (Parametro p JOIN Metodo m ON p.Metodo=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
				WHERE p.CodAuto='$id'"; //Query per recuperare il parametro
		$attr=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($attr);
		if($row[0]==$id){
			$title="$row[6] - $row[4] - Elimina $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>$row[6] - $row[4] - Elimina $row[1]</h2>
				<p>Sei sicuro di voler eliminare il seguente parametro?</p>
				<table>
					<thead>
						<tr>
							<th>Nome</th>
							<th>Tipo</th>
							<th>Descrizione</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			par_table($row);
echo<<<END

						</tr>
					</tbody>
				</table>
				<div id="form">
					<form action="$absurl/Classi/Metodi/Parametri/eliminaparametro.php?id=$id" method="post">
						<fieldset>
							<input type="hidden" id="me" name="me" value="$row[5]" />
							<input type="hidden" id="cl" name="cl" value="$row[7]" />
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
			$title="Elimina Parametro - Parametro Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il parametro con id "$id" non è presente nel database.</p>
			</div>
END;
		}
	}
	endpage_builder();
}
?>