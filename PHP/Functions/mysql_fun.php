<?php

function extract_IdRequisiti($tipo){
	$conn=sql_conn();
	$query="SELECT r.CodAuto, r.IdRequisito
			FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
			WHERE r.Tipo='$tipo'
			ORDER BY h.Position";
	$requi=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	return $requi;
}

function fail($message){
	die($_SERVER['PHP_SELF'] . ": $message<br />");
}

function get_info($user){
	$user=mysql_escape_string($user);
	$conn=sql_conn();
	$query="SELECT u.Password, u.Nome, u.Cognome
			FROM Utenti u
			WHERE u.Username='$user'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$db=mysql_fetch_row($query);
	return $db;
}

function sql_conn(){
	$host="INSERIRE_NOME_HOST";
	$user="INSERIRE_NOME_UTENTE_DB";
	$pwd="INSERIRE_PASSWD_DB";
	$dbname="INSERIRE_NOME_DB";
	$conn=mysql_connect($host,$user,$pwd)
			or fail("Connessione fallita!");
	mysql_select_db($dbname);
	$query="SET @@session.max_sp_recursion_depth = 255";//necessario per garantire
	//max profondità possibile alle procedure ricorsive nei sistemi che non 
	//permettono di settare variabili globali
    $query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	return $conn;
}

?>