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
		header("Location: $absurl/UseCase/usecase.php");
	}
	elseif(isset($_REQUEST['yes'])){
		$id=$_GET['id'];
		$timestampf=$_POST["timestamp"];
		$conn=sql_conn();
		$timestamp_query="SELECT u.Time
						  FROM UseCase u
						  WHERE u.CodAuto='$id'";
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
				<p>Lo use case è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/UseCase/eliminausecase.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
			</div>
END;
			}
			else{
				$query="CALL removeUseCase('$id')";
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Use Case Eliminato";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Lo use case è stato eliminato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/UseCase/usecase.php">Torna a Use Case</a>.</p>
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
				<p>Lo use case è già stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/UseCase/usecase.php">Torna a Use Case</a>.</p>
			</div>
END;
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT u1.CodAuto, u1.IdUC, u1.Nome, u1.Diagramma, u1.Descrizione, u1.Precondizioni, u1.Postcondizioni, u1.Padre, u1.ScenarioPrincipale, u1.Inclusioni, u1.Estensioni, u1.ScenariAlternativi, u1.Time, u2.IdUC
				FROM UseCase u1 LEFT JOIN UseCase u2 ON u1.Padre=u2.CodAuto
				WHERE u1.CodAuto='$id'";
		$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($uc);
		if($row[0]==$id){
			$title="Elimina Use Case - $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Elimina - $row[1]</h2>
				<p>Sei sicuro di voler eliminare il seguente requisito?</p>
				<table>
					<thead>
						<tr>
							<th>IdUC</th>
							<th>Nome</th>
							<th>Diagramma</th>
							<th>Descrizione</th>
							<th>Precondizioni</th>
							<th>Postcondizioni</th>
							<th>Padre</th>
							<th>ScenarioPrincipale</th>
							<th>Inclusioni</th>
							<th>Estensioni</th>
							<th>ScenariAlternativi</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			uc_table($row);
echo<<<END
						</tr>
					</tbody>
				</table>
				<div id="form">
					<form action="$absurl/UseCase/eliminausecase.php?id=$id" method="post">
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
			$title="Elimina Use Case - Use Case Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Lo use case con id "$id" non è presente nel database.</p>
			</div>
END;
		}
	}
	endpage_builder();
}
?>