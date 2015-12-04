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
		$nomef=$_POST["nome"];
		$descf=$_POST["desc"];
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
				<h2>Errore nell'inserimento dell'attore</h2>
				<p>Non è stato inserito correttamente il campo 'Nome'. <a class="link-color-pers" href="$absurl/Attori/inserisciattore.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
			$nomef=mysql_escape_string($nomef);
			$descf=mysql_escape_string($descf);
			$conn=sql_conn();
			$query="CALL insertAttore('$nomef','$descf');";
			$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$title="Attore Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>L'attore è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Attori/attori.php">Torna a Attori</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Inserisci Attore";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Inserisci Attore</h2>
				<div id="form">
					<form action="$absurl/Attori/inserisciattore.php" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="20" />
							</p>
							<p>
								<label for="desc">Descrizione:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<input type="submit" id="submit" name="submit" value="Inserisci" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
			</div>
END;
	}
	endpage_builder();
}
?>