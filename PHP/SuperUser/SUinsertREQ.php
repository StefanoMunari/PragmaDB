<?php

require('../Functions/urlLab.php');

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
	$reqPage= array();
	$reqRecord= array();
	$data= $_POST["page"];
	if(!(empty($data)))
	{
		$escape="";
		$splitValue= "**";
		$splitRecord= "!!!!";
		$pageIndex= 0;
		while($data !== false)
		{
			$record= strtok($data,$splitRecord);
			$data= substr($data,strlen($record."!!!!"));//Un utente può creare un nuovo progetto**Funzionale**Obbligatorio**NULL**0**0**F1**''**!!!!
			$record= str_replace("!!!!", "**", $record);
			while($record !==false)
			{
				$token= strtok($record,$splitValue);
				$record= substr($record,strlen($token."**"));//Un utente può creare un nuovo progetto**
				$reqRecord[0]= "smun";
				$recordIndex= 1;

				while($record !==false)
				{
					$token= trim(preg_replace('/\s+/', ' ', $token));
					$escape= mysql_escape_string($token);
					$reqRecord[$recordIndex]= $escape;
					$token= strtok($record,$splitValue);
					$record= substr($record,strlen($token."**"));//Funzionale**
					$recordIndex= $recordIndex+1;
				}
				$escape= mysql_escape_string($token);
				$reqRecord[$recordIndex]= $escape;
			}
			if(count($reqRecord) == 9)
			{
				$reqPage[$pageIndex]= array($reqRecord[0],$reqRecord[1],$reqRecord[2],$reqRecord[3],$reqRecord[4],$reqRecord[5],$reqRecord[6],$reqRecord[7],$reqRecord[8]);
				$pageIndex= $pageIndex+1;
			}
		}
		/*STAMPA
		if(!empty($reqPage))
		{
			for ($pageIndex=0; $pageIndex < count($reqPage); $pageIndex++) 
			{
				for ($recordIndex=0; $recordIndex < count($reqRecord); $recordIndex++)
				{
					echo $reqPage[$pageIndex][$recordIndex]."<br>";
				}
			}
		}*/
		//INSERIMENTO NEL DB
		if(!empty($reqPage))
		{
			$query="";
			$conn= sql_conn_testDB();
			for ($pageIndex=0; $pageIndex < count($reqPage); $pageIndex++) 
			{	
				/*RICAVO GLI ID RELATIVI*/
				if($reqPage[$pageIndex][4] != "NULL")
				{
					$padre= $reqPage[$pageIndex][4];
					$query= "SELECT r.CodAuto FROM Requisiti r WHERE r.IdRequisito= '$padre'";
					$ris= mysql_query($query,$conn) or die("Query fallita: ".mysql_error($conn));
					$row=mysql_fetch_row($ris);
					$reqPage[$pageIndex][4]= $row[0];
				}
				$reqPage[$pageIndex][7]= substr($reqPage[$pageIndex][7],1);
				$var0=$reqPage[$pageIndex][0];
				$var1=$reqPage[$pageIndex][1];
				$var2=$reqPage[$pageIndex][2];
				$var3=$reqPage[$pageIndex][3];
				$var4=$reqPage[$pageIndex][4];
				$var5=$reqPage[$pageIndex][5];
				$var6=$reqPage[$pageIndex][6];
				$var7=$reqPage[$pageIndex][7];
				$var8=$reqPage[$pageIndex][8];
				/*ESEGUO LA INSERT*/
				if($reqPage[$pageIndex][4] == "NULL")
					$query= "CALL insertRequisito('$var0','$var1','$var2','$var3',$var4,$var5,$var6,$var7,'')";
				else
					$query= "CALL insertRequisito('$var0','$var1','$var2','$var3','$var4',$var5,$var6,$var7,'')";
				echo $query;
				mysql_query($query,$conn) or die("Query fallita: ".mysql_error($conn));
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