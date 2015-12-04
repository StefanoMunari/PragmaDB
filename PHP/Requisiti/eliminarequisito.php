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
		header("Location: $absurl/Requisiti/requisiti.php");
	}
	elseif(isset($_REQUEST['yes'])){
		$id=$_GET['id'];
		$timestampf=$_POST["timestamp"];
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
				<h2>Errore nell' eliminazione:</h2>
				<p>Il requisito è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Requisiti/eliminarequisito.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
			</div>
END;
			}
			else{
				$query="CALL removeRequisito('$id')";
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Requisito Eliminato";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il requisito è stato eliminato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Torna a Requisiti</a>.</p>
			</div>
END;
			}
		}
		else{
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'eliminazione:</h2>
				<p>Il requisito è già stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Torna a Requisiti</a>.</p>
			</div>
END;
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT r1.CodAuto,r1.IdRequisito,r1.Descrizione,r1.Tipo,r1.Importanza,r1.Padre,r1.Stato,r1.Soddisfatto,r1.Implementato,r1.Fonte,r2.IdRequisito,f.Nome
				FROM (Requisiti r1 LEFT JOIN Requisiti r2 ON r1.Padre=r2.CodAuto) JOIN Fonti f ON r1.Fonte=f.CodAuto
				WHERE r1.CodAuto='$id'";
		$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($req);
		if($row[0]==$id){
			$title="Elimina Requisito - $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Elimina - $row[1]</h2>
				<p>Sei sicuro di voler eliminare il seguente requisito?</p>
				<table>
					<thead>
						<tr>
							<th>IdRequisito</th>
							<th>Descrizione</th>
							<th>Tipo</th>
							<th>Importanza</th>
							<th>Padre</th>
							<th>Stato</th>
							<th>Soddisfatto</th>
							<th>Implementato</th>
							<th>Fonte</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			requisito_table($row);
echo<<<END
						</tr>
					</tbody>
				</table>
				<div id="form">
					<form action="$absurl/Requisiti/eliminarequisito.php?id=$id" method="post">
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
			$title="Elimina Requisito - Requisito Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il requisito con id "$id" non è presente nel database.</p>
			</div>
END;
		}
	}
	endpage_builder();
}
?>