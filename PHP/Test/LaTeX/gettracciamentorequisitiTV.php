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
	header('Content-Disposition: attachment; filename="tracciamentoRequisitiTV.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_requi="SELECT r.IdRequisito, CONCAT('TV',SUBSTRING(r.IdRequisito,2))
			   FROM (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) JOIN Test t ON r.CodAuto=t.Requisito
			   WHERE t.Tipo='Validazione'
			   ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$requi=mysql_query($query_requi,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Requisiti-Test di Validazione}
\\normalsize
\\begin{longtable}{|>{\centering}m{5cm}|m{5cm}<{\centering}|}
\\hline 
\\textbf{Requisito} & \\textbf{Test}\\\
\\hline
\\endhead
END;
	while($row_requi=mysql_fetch_row($requi)){
echo<<<END

$row_requi[0] & \\hyperlink{{$row_requi[1]}}{{$row_requi[1]}}\\\ \\hline
END;
	}
echo<<<END

\\caption[Tracciamento Requisiti-Test di Validazione]{Tracciamento Requisiti-Test di Validazione}
\\label{tabella:requi-tv}
\\end{longtable}
\\clearpage

END;
}
?>