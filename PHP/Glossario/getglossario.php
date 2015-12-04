<?php

require('../Functions/get_tex.php');
require('../Functions/mysql_fun.php');
require('../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	if($_GET["dHjhlCaf"]=="8sdfjhG34239bj3r459srglQjhq3r"){
		glossarioTex();
	}
	else{
		header("Location: $absurl/error.php");
	}
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="terminiGlossario.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	glossarioTex();
}
?>