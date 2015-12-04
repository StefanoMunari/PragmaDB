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
		$id=$_GET['id'];
		$me=$_POST["me"];
		$cl=$_POST["cl"];
		$nomef=$_POST["nome"];
		$old_nomef=$_POST["old_nome"];
		$tipof=$_POST["tipo"];
		$old_tipof=$_POST["old_tipo"];
		$descf=$_POST["desc"];
		$old_descf=$_POST["old_desc"];
		$timestampf=$_POST["timestamp"];
		$err_no_modifica=false;
		$err_nome=false;
		$err_tipo=false;
		$err_desc=false;
		$err_pres=false;
		$errori=0;
		if(($nomef==$old_nomef) && ($tipof==$old_tipof) && ($descf==$old_descf)){
			$err_no_modifica=true;
			$errori++;
		}
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
				WHERE p.Nome='$nomef' AND p.Metodo='$me' AND p.CodAuto<>'$id'";
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
				<h2>Errore nella modifica dei seguenti campi:</h2>
				<ul>
END;
			if($err_no_modifica){
echo<<<END

					<li>Nessun campo è stato modificato!</li>
END;
			}
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
				<p><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/modificaparametro.php?id=$id">Riprova</a>.</p>
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
				<h2>Errore nella modifica:</h2>
				<p>La classe contenitore è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$me">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL modifyParametro('$id',";
					if($nomef==$old_nomef){
						$query=$query."null,";
					}
					else{
						$query=$query."'$nomef',";
					}
					if($tipof==$old_tipof){
						$query=$query."null,";
					}
					else{
						$query=$query."'$tipof',";
					}
					if($descf==$old_descf){
						$query=$query."null,";
					}
					else{
						$query=$query."'$descf',";
					}
					$query=$query."'$me','$cl')";
					$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
					$title="Parametro Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il parametro è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$me">Torna a Parametri</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>La classe contenitore è stata eliminata da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT p.CodAuto, p.Nome, p.Tipo, p.Descrizione, m.Nome, p.Metodo, c.PrefixNome, c.CodAuto
				FROM (Parametro p JOIN Metodo m ON p.Metodo=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
				WHERE p.CodAuto='$id'";
		$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$pardb=mysql_fetch_row($par);
		if($pardb[0]==$id){
			$title="$pardb[6] - $pardb[4] - Modifica $pardb[1]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$pardb[6] - $pardb[4] - Modifica $pardb[1]</h2>
				<div id="form">
					<form action="$absurl/Classi/Metodi/Parametri/modificaparametro.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="800" value="$pardb[1]" />
							</p>
							<p>
								<label for="tipo">Tipo*:</label>
								<input type="text" id="tipo" name="tipo" maxlength="800" value="$pardb[2]" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$pardb[3]</textarea>
							</p>
							<input type="hidden" id="old_nome" name="old_nome" value="$pardb[1]" />
							<input type="hidden" id="old_tipo" name="old_tipo" value="$pardb[2]" />
							<input type="hidden" id="old_desc" name="old_desc" value="$pardb[3]" />
							<input type="hidden" id="me" name="me" value="$pardb[5]" />
							<input type="hidden" id="cl" name="cl" value="$pardb[7]" />
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
			$title="Modifica Parametro - Parametro Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il parametro con id "$id" non è presente nel database.</p>
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