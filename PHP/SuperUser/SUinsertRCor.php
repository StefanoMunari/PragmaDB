<?php

require('../Functions/urlLab.php');

/*
CHUNCKS:
	* -> IdUC_separator
	, -> IdRequisito_separator
	!!! -> UCReqCorrelatiBlock_separator
STRING EXAMPLE:
	IdUC*IdReqcorrelato1,IdReqCorrelato2,IdReqCorrelato3,!!!
*/

$absurl=urlbasesito();

function sql_conn_testDB(){
	$host="INSERIRE_NOME_HOST";
	$user="INSERIRE_NOME_UTENTE_DB";
	$pwd="INSERIRE_PASSWD_DB";
	$dbname="INSERIRE_NOME_DB";
	$conn=mysql_connect($host,$user,$pwd)
			or die($_SERVER['PHP_SELF'] . ": Connessione Fallita!<br />");
	mysql_select_db($dbname);
	$query="SET @@session.max_sp_recursion_depth = 255";
    $query=mysql_query($query,$conn) or die($_SERVER['PHP_SELF'] ."Query fallita: ".mysql_error($conn));
	return $conn;
}

if(isset($_REQUEST['submit'])){
echo<<<END
<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="UTF-8" />
		<title>Risultato SuperUser - PragmaDB</title>
	</head>
	<body>
END;
	$data= $_POST["page"];
	if(!(empty($data)))
	{
		$escape="";
		$splitId= "*";
		$splitUC= "!!!";
		$UC="";
		$listReq="";
		$query="";
		$conn= sql_conn_testDB();
		while($data !== false)
		{
			$UC= strtok($data,$splitUC);
			$data= substr($data,strlen($UC.$splitUC));
			$listReq= substr($UC,strpos($UC,$splitId,2)+1);
			$UC= strtok($UC,$splitId);
			$UC= trim($UC);
			if($UC != '' && $listReq != '')
			{
				$query= "CALL SUinsertRQ('$UC','$listReq')";
				mysql_query($query,$conn) or die("Query fallita: ".mysql_error($conn));
			}
			else
			{
				if($UC != '')
					echo "ERRORE: LISTA REQUISITI VUOTA!";
				else
					echo "ERRORE: USE CASE VUOTO!";
			}
		}	
	}
echo<<<END

	</body>
</html>
END;
}
else{
	header("Location: $absurl/error.php");
}
?>