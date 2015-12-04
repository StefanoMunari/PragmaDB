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
	header('Content-Disposition: attachment; filename="tracciamentoTSRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_ts="SELECT CONCAT('TS',SUBSTRING(r.IdRequisito,2)), r.IdRequisito
			   FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
			   WHERE t.Tipo='Sistema'
			   ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$ts=mysql_query($query_ts,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Test di Sistema-Requisiti}
\\normalsize
\\begin{longtable}{|>{\centering}m{5cm}|m{5cm}<{\centering}|}
\\hline 
\\textbf{Test} & \\textbf{Requisito}\\\
\\hline
\\endhead
END;
	while($row_ts=mysql_fetch_row($ts)){
echo<<<END

\\hyperlink{{$row_ts[0]}}{{$row_ts[0]}} & $row_ts[1]\\\ \\hline
END;
	}
echo<<<END

\\caption[Tracciamento Test di Sistema-Requisiti]{Tracciamento Test di Sistema-Requisiti}
\\label{tabella:ts-requi}
\\end{longtable}
\\clearpage

END;
}
?>