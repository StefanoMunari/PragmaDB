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
	if(isset($_REQUEST['submit'])){
		$me=$_GET['me'];
		$cl=$_POST['cl'];
		$nomef=$_POST["nome"];
		$tipof=$_POST["tipo"];
		$descf=$_POST["desc"];
		$timestampf=$_POST["timestamp"];
		$err_nome=false;
		$err_tipo=false;
		$err_desc=false;
		$err_pres=false;
		$errori=0;
		if($nomef==null){
			$err_nome=true;
			$errori++;
		}
		if($tipof==null){
			$err_tipo=true;
			$errori++;
		}
		if($descf==null){
			$err_desc=true;
			$errori++;
		}
		$nomef=mysql_escape_string($nomef);
		$tipof=mysql_escape_string($tipof);
		$descf=mysql_escape_string($descf);
		$conn=sql_conn();
		$query="SELECT p.CodAuto
				FROM Parametro p
				WHERE p.Nome='$nomef' AND p.Metodo='$me'";
		$pres=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$pres=mysql_fetch_row($pres);
		if($pres[0]!=null){
			$err_pres=true;
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
			if($err_tipo){
echo<<<END

					<li>Tipo: NON INSERITO</li>
END;
			}
			if($err_desc){
echo<<<END

					<li>Descrizione: NON INSERITA</li>
END;
			}
			if($err_pres){
echo<<<END

					<li>IL PARAMETRO E' GIA' PRESENTE NEL DB!</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/inserisciparametro.php?me=$me">Riprova</a>.</p>
END;
		}
		else{
			$timestamp_query="SELECT c.Time
							  FROM Classe c
							  WHERE c.CodAuto='$cl'";
			$timestamp_query=mysql_query($timestamp_query,$conn) or fail("Query fallita: ".mysql_error($conn));
			if($row=mysql_fetch_row($timestamp_query)){
				$timestamp_db=$row[0];
				$timestamp_db=strtotime($timestamp_db);
				if($timestampf<$timestamp_db){
					$title="Errore";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento:</h2>
				<p>La classe contenitore è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$me">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL insertParametro('$nomef','$tipof','$descf','$me','$cl')";
					$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
					$title="Parametro Inserito";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il parametro è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$me">Torna a Parametri</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento:</h2>
				<p>La classe contenitore è stata eliminata da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
			}
		}
	}
	else{
		$me=$_GET['me'];
		$me=mysql_escape_string($me);
		$conn=sql_conn();
		$query="SELECT m.CodAuto, m.Nome, m.Classe
				FROM Metodo m
				WHERE m.CodAuto='$me'";
		$metodo=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$row_me=mysql_fetch_row($metodo);
		if($row_me[0]==$me){
			$title="$row_me[1] - Inserisci Parametro";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$row_me[1] - Inserisci Parametro</h2>
				<div id="form">
					<form action="$absurl/Classi/Metodi/Parametri/inserisciparametro.php?me=$me" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="800" />
							</p>
							<p>
								<label for="tipo">Tipo*:</label>
								<input type="text" id="tipo" name="tipo" maxlength="800" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<input type="hidden" id="cl" name="cl" value="$row_me[2]" />
							<input type="hidden" id="timestamp" name="timestamp" value="$timestamp" />
							<p>
								<input type="submit" id="submit" name="submit" value="Inserisci" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
END;
		}
		else{
			$title="Inserisci Parametro - Metodo Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il metodo con id "$me" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>