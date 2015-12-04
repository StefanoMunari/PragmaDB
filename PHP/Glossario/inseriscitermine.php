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
		$identificativof=$_POST["identificativo"];
		$namef=$_POST["name"];
		$descf=$_POST["desc"];
		$firstf=$_POST["first"];
		$firstpluralf=$_POST["firstplural"];
		$textf=$_POST["text"];
		$pluralf=$_POST["plural"];
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
				<h2>Errore nell'inserimento dei seguenti campi:</h2>
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
				<p><a class="link-color-pers" href="$absurl/Glossario/inseriscitermine.php">Riprova</a>.</p>
			</div>
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
			$query="CALL insertGlossario('$identificativof','$namef','$descf',";
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
			$title="Termine Glossario Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il termine è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Inserisci Termine Glossario";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Inserisci Termine Glossario</h2>
				<div id="form">
					<form action="$absurl/Glossario/inseriscitermine.php" method="post">
						<fieldset>
							<p>
								<label for="identificativo">Identificativo*:</label>
								<input type="text" id="identificativo" name="identificativo" maxlength="50" />
							</p>
							<p>
								<label for="nome">Name*:</label>
								<input type="text" id="name" name="name" maxlength="50" />
							</p>
							<p>
								<label for="desc">Description*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="nome">First:</label>
								<input type="text" id="first" name="first" maxlength="50" />
							</p>
							<p>
								<label for="nome">First Plural:</label>
								<input type="text" id="firstplural" name="firstplural" maxlength="50" />
							</p>
							<p>
								<label for="nome">Text:</label>
								<input type="text" id="text" name="text" maxlength="50" />
							</p>
							<p>
								<label for="nome">Plural:</label>
								<input type="text" id="plural" name="plural" maxlength="50" />
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