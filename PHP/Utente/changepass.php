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
		$user=$_SESSION['user'];
		$oldp=$_POST["old"];
		$newp=$_POST["new"];
		$confp=$_POST["conf"];
		$dbinfo=get_info($user);
		$dbpass=$dbinfo[0];
		if(sha1($oldp) == $dbpass){
			if($newp!=$confp){
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica della password</h2>
				<p>La nuova password non coincide con la conferma. <a class="link-color-pers" href="$absurl/Utente/changepass.php">Riprova</a>.</p>
			</div>
END;
			}
			elseif(strlen($newp)<8){
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica della password</h2>
				<p>La nuova password deve avere 8 caratteri. <a class="link-color-pers" href="$absurl/Utente/changepass.php">Riprova</a>.</p>
			</div>
END;
			}
			else{
				$newp=sha1($newp);
				$newp=mysql_escape_string($newp);
				$conn=sql_conn();
				$query="UPDATE Utenti u
						SET u.Password='$newp'
						WHERE u.Username='$user'";
				$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				$title="Password Modificata";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>La password è stata modificata con successo.</p>
			</div>
END;
			}
		}
		else{
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica della password</h2>
				<p>Password attuale non corretta. <a class="link-color-pers" href="$absurl/Utente/changepass.php">Riprova</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Cambia Password";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Cambia Password</h2>
				<div id="form">
					<h4>Inserisci vecchia e nuova password</h4>
					<form action="$absurl/Utente/changepass.php" method="post">
						<fieldset>
							<p>
								<label for="old">Vecchia Password*:</label>
								<input type="password" id="old" name="old" maxlength="8" />
							</p>
							<p>
								<label for="new">Nuova Password*:</label>
								<input type="password" id="new" name="new" maxlength="8" />
							</p>
							<p>
								<label for="new">Conferma Password*:</label>
								<input type="password" id="conf" name="conf" maxlength="8" />
							<p>
								<input type="submit" id="submit" name="submit" value="Cambia" />
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