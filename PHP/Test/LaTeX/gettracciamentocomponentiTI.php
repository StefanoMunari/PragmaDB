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
	header('Content-Disposition: attachment; filename="tracciamentoComponentiTI.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query_pkg="SELECT p.PrefixNome, t.IdTest
				FROM Package p JOIN Test t ON p.CodAuto=t.Package
				WHERE t.Tipo='Integrazione'
				ORDER BY p.PrefixNome";
	$pkg=mysql_query($query_pkg,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Componenti-Test di Integrazione}
\\normalsize
\\begin{longtable}{|>{\centering}m{9cm}|m{3cm}<{\centering}|}
\\hline 
\\textbf{Componente} & \\textbf{Test}\\\
\\hline
\\endhead
END;
	while($row_pkg=mysql_fetch_row($pkg)){
echo<<<END

\\nogloxy{\\texttt{{$row_pkg[0]}}} & \\hyperlink{{$row_pkg[1]}}{{$row_pkg[1]}}\\\ \\hline
END;
	}
echo<<<END

\\caption[Tracciamento Componenti-Test di Integrazione]{Tracciamento Componenti-Test di Integrazione}
\\label{tabella:pkg-ti}
\\end{longtable}
\\clearpage

END;
}
?>