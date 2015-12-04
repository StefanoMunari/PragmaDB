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
	header('Content-Disposition: attachment; filename="tracciamentoComponentiRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_update="CALL automatizeRequisitiPackage()";
	$query_pkg="SELECT DISTINCT p.CodAuto, p.PrefixNome
				FROM Package p JOIN RequisitiPackage rp ON p.CodAuto=rp.CodPkg
				WHERE p.PrefixNome<>'Premi'
				ORDER BY p.PrefixNome";
	//$upd=mysql_query($query_update,$conn) or fail("Query fallita: ".mysql_error($conn));
	$pkg=mysql_query($query_pkg,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Componenti-Requisiti}
\\normalsize
\\begin{longtable}{|>{\centering}m{10cm}|m{3cm}<{\centering}|}
\\hline 
\\textbf{Componente} & \\textbf{Requisiti}\\\
\\hline
\\endhead
END;
	//$query_ord="CALL sortForest('Requisiti')";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	while($row_pkg=mysql_fetch_row($pkg)){
		componentiRequisitiTex($conn, $row_pkg);
	}
echo<<<END

\\caption[Tracciamento Componenti-Requisiti]{Tracciamento Componenti-Requisiti}
\\label{tabella:pack-requi}
\\end{longtable}
\\clearpage

END;
}
?>