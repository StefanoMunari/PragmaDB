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
	header('Content-Disposition: attachment; filename="tracciamentoRequisitiFonti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_requi="SELECT r1.CodAuto,r1.IdRequisito,f.Nome
					FROM (_MapRequisiti h JOIN Requisiti r1 ON h.CodAuto=r1.CodAuto) JOIN Fonti f ON r1.Fonte=f.CodAuto
					ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$requi=mysql_query($query_requi,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Requisiti-Fonti}
\\normalsize
\\begin{longtable}{|>{\centering}m{5cm}|m{5cm}<{\centering}|}
\\hline 
\\textbf{Id Requisito} & \\textbf{Fonti}\\\
\\hline
\\endhead
END;
	//$query_ord="CALL sortForest('UseCase')";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	while($row_requi=mysql_fetch_row($requi)){
		requisitiFontiTex($conn, $row_requi);
	}
echo<<<END

\\caption[Tracciamento Requisiti-Fonti]{Tracciamento Requisiti-Fonti}
\\label{tabella:requi-fonti}
\\end{longtable}
\\clearpage

END;
}
?>