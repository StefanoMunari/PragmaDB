<?php

require('../../Functions/mysql_fun.php');
require('../../Functions/page_builder.php');
require('../../Functions/urlLab.php'); 

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	if(isset($_REQUEST['submit'])){
		$id=$_GET['id'];
		$cl=$_POST["cl"];
		$accf=$_POST["acc"];
		$old_accf=$_POST["old_acc"];
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
		if(($accf==$old_accf) && ($nomef==$old_nomef) && ($tipof==$old_tipof) && ($descf==$old_descf)){
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
		$accf=mysql_escape_string($accf);
		$nomef=mysql_escape_string($nomef);
		$tipof=mysql_escape_string($tipof);
		$descf=mysql_escape_string($descf);
		$conn=sql_conn();
		$query="SELECT a.CodAuto
				FROM Attributo a
				WHERE a.Nome='$nomef' AND a.Classe='$cl' AND a.CodAuto<>'$id'";
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

					<li>L'ATTRIBUTO E' GIA' PRESENTE NEL DB!</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Classi/Attributi/modificaattributo.php?id=$id">Riprova</a>.</p>
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
				<p>La classe contenitore è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$cl">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL modifyAttributo('$id',";
					if($accf==$old_accf){
						$query=$query."null,";
					}
					else{
						$query=$query."'$accf',";
					}
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
					$query=$query."'$cl')";
					$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
					$title="Attributo Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>L'attributo è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$cl">Torna ad Attributi</a>.</p>
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
		$query="SELECT a.CodAuto, a.AccessMod, a.Nome, a.Tipo, a.Descrizione, c.PrefixNome, a.Classe
				FROM Attributo a JOIN Classe c ON a.Classe=c.CodAuto
				WHERE a.CodAuto='$id'";
		$attr=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$attrdb=mysql_fetch_row($attr);
		if($attrdb[0]==$id){
			$title="$attrdb[5] - Modifica $attrdb[2]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$attrdb[5] - Modifica $attrdb[2]</h2>
				<div id="form">
					<form action="$absurl/Classi/Attributi/modificaattributo.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="acc1">Accessibilità*:</label>
END;
			if($attrdb[1]=="-"){
echo<<<END

								<input type="radio" id="acc1" name="acc" value="-" checked="checked" /> <span class="mancante">- (Private)</span>
END;
			}
			else{
echo<<<END

								<input type="radio" id="acc1" name="acc" value="-" /> <span class="mancante">- (Private)</span>
END;
			}
			if($attrdb[1]=="#"){
echo<<<END

								<input type="radio" id="acc2" name="acc" value="#" checked="checked"/> # (Protected)
END;
			}
			else{
echo<<<END

								<input type="radio" id="acc2" name="acc" value="#" /> # (Protected)
END;
			}
			if($attrdb[1]=="+"){
echo<<<END

								<input type="radio" id="acc3" name="acc" value="+" checked="checked" /> <span class="completato">+ (Public)</span>
END;
			}
			else{
echo<<<END

								<input type="radio" id="acc3" name="acc" value="+" /> <span class="completato">+ (Public)</span>
END;
			}
echo<<<END

							</p>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="800" value="$attrdb[2]" />
							</p>
							<p>
								<label for="tipo">Tipo*:</label>
								<input type="text" id="tipo" name="tipo" maxlength="800" value="$attrdb[3]" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$attrdb[4]</textarea>
							</p>
							<input type="hidden" id="old_acc" name="old_acc" value="$attrdb[1]" />
							<input type="hidden" id="old_nome" name="old_nome" value="$attrdb[2]" />
							<input type="hidden" id="old_tipo" name="old_tipo" value="$attrdb[3]" />
							<input type="hidden" id="old_desc" name="old_desc" value="$attrdb[4]" />
							<input type="hidden" id="cl" name="cl" value="$attrdb[6]" />
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
			$title="Modifica Attributo - Attributo Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>L'attributo con id "$id" non è presente nel database.</p>
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