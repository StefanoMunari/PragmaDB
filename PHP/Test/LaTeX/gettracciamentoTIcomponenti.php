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
	header('Content-Disposition: attachment; filename="tracciamentoTIComponenti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query_ti="SELECT t.IdTest, p.PrefixNome
			   FROM Test t JOIN Package p ON t.Package=p.CodAuto
			   WHERE t.Tipo='Integrazione'
			   ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$ti=mysql_query($query_ti,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Test di Integrazione-Componenti}
\\normalsize
\\begin{longtable}{|>{\centering}m{3cm}|m{9cm}<{\centering}|}
\\hline 
\\textbf{Test} & \\textbf{Componente}\\\
\\hline
\\endhead
END;
	while($row_ti=mysql_fetch_row($ti)){
echo<<<END

\\hyperlink{{$row_ti[0]}}{{$row_ti[0]}} & \\nogloxy{\\texttt{{$row_ti[1]}}}\\\ \\hline
END;
	}
echo<<<END

\\caption[Tracciamento Test di Integrazione-Componenti]{Tracciamento Test di Integrazione-Componenti}
\\label{tabella:ts-requi}
\\end{longtable}
\\clearpage

END;
}
?>