<?php

require('../../Functions/get_tex.php');
require('../../Functions/mysql_fun.php');
require('../../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="tabellaRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$tipi=array('Funzionale','Prestazionale','Qualita','Vincolo');
	$sections=array('Requisiti Funzionali','Requisiti Prestazionali','Requisiti di Qualità','Requisiti di Vincolo');
	$headers=array('Id Requisito','Descrizione','Stato');
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	for($i=0;$i<4;$i++){
		$query="SELECT r1.CodAuto,r1.IdRequisito,r1.Descrizione,r1.Soddisfatto
				FROM _MapRequisiti h JOIN Requisiti r1 ON h.CodAuto=r1.CodAuto
				WHERE r1.Tipo='$tipi[$i]'
				ORDER BY h.Position";
		$requi=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$row=mysql_fetch_row($requi);
		if($row[0]!=null){
echo<<<END
\\subsection{{$sections[$i]}}
\\normalsize
\\begin{longtable}{|c|>{\centering}m{7cm}|c|}
\\hline 
\\textbf{{$headers[0]}} & \\textbf{{$headers[1]}} & \\textbf{{$headers[2]}}\\\
\\hline
\\endhead
END;
			//$query_ord="CALL sortForest('UseCase')";
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			requisitiTex($conn, $row);
			while($row=mysql_fetch_row($requi)){
				requisitiTex($conn, $row);
			}
echo<<<END

\\caption[$sections[$i]]{{$sections[$i]}}
\\label{tabella:req$i}
\\end{longtable}
\\clearpage

END;
		}
	}
}
?>