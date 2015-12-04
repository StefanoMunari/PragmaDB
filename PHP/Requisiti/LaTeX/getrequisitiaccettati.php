<?php

require('../../Functions/mysql_fun.php');
require('../../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="requisitiAccettati.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query="SELECT r.IdRequisito, r.Descrizione
			FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
			WHERE r.Stato='1'
			ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$requi=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\begin{itemize}
END;
	while($row=mysql_fetch_row($requi)){
echo<<<END

\\item \\hyperlink{{$row[0]}}{{$row[0]}}: $row[1];
END;
	}
echo<<<END

\\end{itemize}
END;
}
?>