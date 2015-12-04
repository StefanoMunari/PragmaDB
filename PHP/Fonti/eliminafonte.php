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
		header("Location: $absurl/Fonti/fonti.php");
	}
	elseif(isset($_REQUEST['yes'])){
		$id=$_GET['id'];
		$timestampf=$_POST["timestamp"];
		$conn=sql_conn();
		$timestamp_query="SELECT f.Time
						  FROM Fonti f
						  WHERE f.CodAuto='$id'";
		$timestamp_query=mysql_query($timestamp_query,$conn) or fail("Query fallita: ".mysql_error($conn));
		if($row=mysql_fetch_row($timestamp_query)){
			$timestamp_db=$row[0];
			$timestamp_db=strtotime($timestamp_db);
			if($timestampf<$timestamp_db){
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'eliminazione:</h2>
				<p>La fonte è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Fonti/eliminafonte.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
			</div>
END;
			}
			else{
				$query="CALL removeFonte('$id')";
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Fonte Eliminata";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>La fonte è stata eliminata con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Torna a Fonti</a>.</p>
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
				<p>La fonte è stata eliminata da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Torna a Fonti</a>.</p>
			</div>
END;
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT f.CodAuto, f.IdFonte, f.Nome, f.Descrizione, f.Time
				FROM Fonti f
				WHERE f.CodAuto='$id'";
		$fon=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($fon);
		if($row[0]==$id){
			$title="Elimina Fonte - $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Elimina - $row[1]</h2>
				<p>Sei sicuro di voler eliminare la seguente fonte?</p>
				<table>
					<thead>
						<tr>
							<th>IdFonte</th>
							<th>Nome</th>
							<th>Descrizione</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><a class="link-color-pers" href="$absurl/Fonti/dettagliofonte.php?id=$row[0]">$row[1]</a></td>
							<td>$row[2]</td>
							<td>$row[3]</td>
						</tr>
					</tbody>
				</table>
				<div id="form">
					<form action="$absurl/Fonti/eliminafonte.php?id=$id" method="post">
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
			$title="Elimina Fonte - Fonte Non Trovata";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La fonte con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Torna a Fonti</a>.</p>
			</div>
END;
		}
	}
	endpage_builder();
}
?>