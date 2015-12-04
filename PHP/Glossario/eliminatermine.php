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
		header("Location: $absurl/Glossario/glossario.php");
	}
	elseif(isset($_REQUEST['yes'])){
		$id=$_GET['id'];
		$timestampf=$_POST["timestamp"];
		$conn=sql_conn();
		$timestamp_query="SELECT g.Time
						  FROM Glossario g
						  WHERE g.CodAuto='$id'";
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
				<p>Il termine è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Glossario/eliminatermine.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
			</div>
END;
			}
			else{
				$query="CALL removeGlossario('$id')";
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Termine Glossario Eliminato";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il termine è stato eliminato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
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
				<p>Il termine è stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
			</div>
END;
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT g.CodAuto, g.IdTermine, g.Identificativo, g.Name, g.Description, g.First, g.FirstPlural, g.Text, g.Plural, g.Time
				FROM Glossario g
				WHERE g.CodAuto='$id'";
		$glo=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($glo);
		if($row[0]==$id){
			$title="Elimina Termine Glossario - $row[2]";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Elimina - $row[2]</h2>
				<p>Sei sicuro di voler eliminare il seguente termine dal glossario?</p>
				<table>
					<thead>
						<tr>
							<th>Identificativo</th>
							<th>Name</th>
							<th>Description</th>
							<th>First</th>
							<th>FirstPlural</th>
							<th>Text</th>
							<th>Plural</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			for($i=2;$i<9;$i++){
				if($row[$i]!=null){
echo<<<END

							<td>$row[$i]</td>
END;
				}
				else{
echo<<<END

							<td></td>
END;
				}
			}
echo<<<END

						</tr>
					</tbody>
				</table>
				<div id="form">
					<form action="$absurl/Glossario/eliminatermine.php?id=$id" method="post">
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
			$title="Elimina Termine Glossario - Termine Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il termine con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
			</div>
END;
		}
	}
	endpage_builder();
}
?>