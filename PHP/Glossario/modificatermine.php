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
		$id=mysql_escape_string($id);
		$identificativof=$_POST["identificativo"];
		$namef=$_POST["name"];
		$descf=$_POST["desc"];
		$firstf=$_POST["first"];
		$firstpluralf=$_POST["firstplural"];
		$textf=$_POST["text"];
		$pluralf=$_POST["plural"];
		$old_identificativof=$_POST["old_identificativo"];
		$timestampf=$_POST["timestamp"];
		$err_identificativo=false;
		$err_identificativo_special=false;
		$err_name=false;
		$err_desc=false;
		$errori=0;
		if($identificativof==null){
			$err_identificativo=true;
			$errori++;
		}
		if(preg_match('/[^a-z]/i', $identificativof)>0){
			$err_identificativo_special=true;
			$errori++;
		}
		if($namef==null){
			$err_name=true;
			$errori++;
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
				<h2>Errore nella modifica dei seguenti campi:</h2>
				<ul>
END;
			if($err_identificativo){
echo<<<END

					<li>Identificativo: NON INDICATO</li>
END;
			}
			elseif($err_identificativo_special){
echo<<<END

					<li>Identificativo: DEVE CONTENERE SOLO CARATTERI ALFABETICI (DI CUI IL PRIMO MINUSCOLO) NON SEPARATI DA SPAZI</li>
END;
			}
			if($err_name){
echo<<<END

					<li>Name: NON INDICATO</li>
END;
			}
			if($err_desc){
echo<<<END

					<li>Description: NON INDICATA</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Glossario/modificatermine.php?id=$id">Riprova</a>.</p>
END;
		}
		else{
			$identificativof=lcfirst($identificativof);
			$identificativof=mysql_escape_string($identificativof);
			$namef=mysql_escape_string($namef);
			$descf=mysql_escape_string($descf);
			$firstf=mysql_escape_string($firstf);
			$firstpluralf=mysql_escape_string($firstpluralf);
			$textf=mysql_escape_string($textf);
			$pluralf=mysql_escape_string($pluralf);
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
				<h2>Errore nella modifica:</h2>
				<p>Il termine è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Glossario/modificatermine.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL modifyGlossario('$id',";
					if($identificativof==$old_identificativof){
						$query=$query."null,";
					}
					else{
						$query=$query."'$identificativof',";
					}
					$query=$query."'$namef','$descf',";
					if($firstf==null){
						$query=$query."null,";
					}
					else{
						$query=$query."'$firstf',";
					}
					if($firstpluralf==null){
						$query=$query."null,";
					}
					else{
						$query=$query."'$firstpluralf',";
					}
					if($textf==null){
						$query=$query."null,";
					}
					else{
						$query=$query."'$textf',";
					}
					if($pluralf==null){
						$query=$query."null)";
					}
					else{
						$query=$query."'$pluralf')";
					}
					$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
					$title="Termine Glossario Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il termine è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>Il termine è stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
END;
			}
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
			$title="Modifica Termine Glossario - $row[2]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Modifica - $row[2]</h2>
				<div id="form">
					<form action="$absurl/Glossario/modificatermine.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="identificativo">Identificativo*:</label>
								<input type="text" id="identificativo" name="identificativo" maxlength="50" value="$row[2]" />
							</p>
							<p>
								<label for="nome">Name*:</label>
								<input type="text" id="name" name="name" maxlength="50" value="$row[3]" />
							</p>
							<p>
								<label for="desc">Description*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$row[4]</textarea>
							</p>
							<p>
								<label for="nome">First:</label>
								<input type="text" id="first" name="first" maxlength="50" value="$row[5]" />
							</p>
							<p>
								<label for="nome">First Plural:</label>
								<input type="text" id="firstplural" name="firstplural" maxlength="50" value="$row[6]" />
							</p>
							<p>
								<label for="nome">Text:</label>
								<input type="text" id="text" name="text" maxlength="50" value="$row[7]" />
							</p>
							<p>
								<label for="nome">Plural:</label>
								<input type="text" id="plural" name="plural" maxlength="50" value="$row[8]" />
							</p>
							<input type="hidden" id="old_identificativo" name="old_identificativo" value="$row[2]" />
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
			$title="Modifica Termine Glossario - Termine Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il termine con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>