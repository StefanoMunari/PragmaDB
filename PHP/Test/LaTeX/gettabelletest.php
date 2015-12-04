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
	header('Content-Disposition: attachment; filename="tabelleTest.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$tipi=array('Validazione','Sistema','Integrazione','Unità');
	$hook=array('validazione','sistema','integrazione','unita');
	$sections=array('Test di Validazione','Test di Sistema','Test di Integrazione','Test di Unità');
	$headers=array('Id Test','Descrizione','Stato');
	//$query_ord="CALL sortForest('Requisiti')";
	$queries[]="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Validazione'
				ORDER BY h.Position";
	$queries[]="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Sistema'
				ORDER BY h.Position";
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t
				WHERE t.Tipo='Integrazione'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t
				WHERE t.Tipo='Unita'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$conn=sql_conn();
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	foreach($queries as $ind => $query){
		$test=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$row=mysql_fetch_row($test);
		if($row[0]!=null){
echo<<<END
\\subsection{{$sections[$ind]}}
\\input{sezioni/test_{$hook[$ind]}.tex}
\\normalsize
\\begin{longtable}{|c|>{}m{8cm}|c|}
\\hline 
\\textbf{{$headers[0]}} & \\textbf{{$headers[1]}} & \\textbf{{$headers[2]}}\\\
\\hline
\\endhead
END;
			testTex($conn, $row);
			while($row=mysql_fetch_row($test)){
				testTex($conn, $row);
			}
echo<<<END

\\caption[$sections[$ind]]{{$sections[$ind]}}
\\label{tabella:test$ind}
\\end{longtable}
\\clearpage


END;
		}
	}
}
?>