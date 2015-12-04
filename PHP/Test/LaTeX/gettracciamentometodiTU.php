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
	header('Content-Disposition: attachment; filename="tracciamentoMetodiTU.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query_met="SELECT DISTINCT m.CodAuto,m.AccessMod,m.Nome,m.ReturnType, c.PrefixNome, c.CodAuto
			   FROM (TestMetodi tm JOIN Metodo m ON tm.CodMet=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
			   ORDER BY c.PrefixNome, m.Nome";
	$met=mysql_query($query_met,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Metodi-Test di Unità}
\\normalsize
\\begin{longtable}{|>{\centering}m{12cm}|m{1cm}<{\centering}|}
\\hline 
\\textbf{Metodo} & \\textbf{Test}\\\
\\hline
\\endhead
END;
	while($riga=mysql_fetch_row($met)){
		$prefix=$riga[4]."::".$riga[2]."()";
		$prefix=fixMethodIntoBorder($prefix);
		$query_tu="SELECT DISTINCT t.IdTest
				   FROM TestMetodi tm JOIN Test t ON tm.CodTest=t.CodAuto
				   WHERE tm.CodMet='$riga[0]'
				   ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
		$tu=mysql_query($query_tu,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\nogloxy{\\texttt{{$prefix}}}
END;
		while($row_tu = mysql_fetch_row($tu)){
echo<<<END
 & \\hyperlink{{$row_tu[0]}}{{$row_tu[0]}}\\\
END;
		}
echo<<<END
 \\hline

END;
	}
echo<<<END

\\caption[Tracciamento Metodi-Test di Unità]{Tracciamento Metodi-Test di Unità}
\\label{tabella:met-tu}
\\end{longtable}
\\clearpage

END;
}
?>