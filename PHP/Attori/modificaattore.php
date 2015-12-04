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
		$id=$_GET['id'];
		$nomef=$_POST["nome"];
		$descf=$_POST["desc"];
		$timestampf=$_POST["timestamp"];
		$err_nome=false;
		$errori=0;
		if($nomef==null){
			$err_nome=true;
			$errori++;
		}
		if($errori>0){
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica dell'attore</h2>
				<p>Non è stato inserito correttamente il campo 'Nome'. <a class="link-color-pers" href="$absurl/Attori/modificaattore.php?id=$id">Riprova</a>.</p>
			</div>
END;
		}
		else{
			$nomef=mysql_escape_string($nomef);
			$descf=mysql_escape_string($descf);
			$conn=sql_conn();
			$timestamp_query="SELECT a.Time
							  FROM Attori a
							  WHERE a.CodAuto='$id'";
			$timestamp_query=mysql_query($timestamp_query,$conn) or fail("Query fallita: ".mysql_error($conn));
			if($row=mysql_fetch_row($timestamp_query)){
				$timestamp_db=$row[0];
				$timestamp_db=strtotime($timestamp_db);
				if($timestampf<$timestamp_db){
					$title="Errore";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>L'attore è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Attori/modificaattore.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL modifyAttore('$id','$nomef','$descf');";
					$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
					$title="Attore Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>L'attore è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Attori/attori.php">Torna a Attori</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>L'attore è stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Attori/attori.php">Torna a Attori</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT a.CodAuto, a.Nome, a.Descrizione, a.Time
				FROM Attori a
				WHERE a.CodAuto='$id'";
		$att=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row=mysql_fetch_row($att);
		if($row[0]==$id){
			$title="Modifica Attore - $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Modifica - $row[1]</h2>
				<div id="form">
					<form action="$absurl/Attori/modificaattore.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="20" value="$row[1]"/>
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$row[2]</textarea>
							</p>
							<input type="hidden" id="timestamp" name="timestamp" value="$timestamp" />
							<p>
								<input type="submit" id="submit" name="submit" value="Modifica" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
END;
		}
		else{
			$title="Modifica Attore - Attore Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>L'attore con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Attori/attori.php">Torna a Attori</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>