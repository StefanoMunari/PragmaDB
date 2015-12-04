<?php

require('Functions/mysql_fun.php');
require('Functions/page_builder.php');
require('Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(isset($_REQUEST['submit'])){
	$user=$_POST["username"];
	$pwd=$_POST["password"];
	$db=get_info($user);
	if(($user!=null) && ($pwd!=null) && (sha1($pwd) == $db[0])){
		$_SESSION['user']=$user;
		$_SESSION['nome']=$db[1];
		$_SESSION['cognome']=$db[2];
	}
	header("Location: $absurl/Utente/home.php");
}
else{
	if(empty($_SESSION['user'])){
		$title="Login";
		startpage_builder($title);
echo<<<END

			<div id="immagine">
				<img src="$absurl/Immagini/pragma.png" alt="Logo Pragma" />
			</div>
			<div id="form">
				<h1>Autenticazione</h1>
				<form action="$absurl/index.php" method="post">
					<fieldset>
						<p>
							<label for="username">Username:</label>
							<input type="text" id="username" name="username" maxlength="4" />
						</p>
						<p>
							<label for="password">Password:</label>
							<input type="password" id="password" name="password" maxlength="8" />
						</p>
						<p>
							<input type="submit" id="submit" name="submit" value="Accedi" />
							<input type="reset" id="reset" name="reset" value="Cancella" />
						</p>
					</fieldset>
				</form>
			</div>
END;
		endpage_builder();
	}
	else{
		header("Location: $absurl/Utente/home.php");
	}
}
?>