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
	header('Content-Disposition: attachment; filename="tracciamentoTVRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_tv="SELECT CONCAT('TV',SUBSTRING(r.IdRequisito,2)), r.IdRequisito
			   FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
			   WHERE t.Tipo='Validazione'
			   ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$tv=mysql_query($query_tv,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Test di Validazione-Requisiti}
\\normalsize
\\begin{longtable}{|>{\centering}m{5cm}|m{5cm}<{\centering}|}
\\hline 
\\textbf{Test} & \\textbf{Requisito}\\\
\\hline
\\endhead
END;
	while($row_tv=mysql_fetch_row($tv)){
echo<<<END

\\hyperlink{{$row_tv[0]}}{{$row_tv[0]}} & $row_tv[1]\\\ \\hline
END;
	}
echo<<<END

\\caption[Tracciamento Test di Validazione-Requisiti]{Tracciamento Test di Validazione-Requisiti}
\\label{tabella:tv-requi}
\\end{longtable}
\\clearpage

END;
}
?>