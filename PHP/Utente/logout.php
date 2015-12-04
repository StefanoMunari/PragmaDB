<?php

require('../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(!empty($_SESSION['user'])){
	$sname=session_name();
	$_SESSION = array();
	session_destroy();
	if(isset($_COOKIE[$sname])){
		setcookie($sname,'',time()-3600,'/');
	}
}

header("Location: $absurl/index.php");

?>
