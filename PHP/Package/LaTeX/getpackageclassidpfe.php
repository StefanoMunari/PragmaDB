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
	header('Content-Disposition: attachment; filename="packageClassiDP-FE.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query="SELECT p1.CodAuto, p1.PrefixNome, p1.UML, p1.Descrizione, p1.Padre, p2.PrefixNome, p2.Nome
			FROM Package p1 LEFT JOIN Package p2 ON p1.Padre=p2.CodAuto
			WHERE LEFT(p1.PrefixNome,16)='Premi::Front-End'
			ORDER BY p1.PrefixNome";
	$pack=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	while($row=mysql_fetch_row($pack)){
echo<<<END
\\subsection{\\nogloxy{{$row[1]}}}
\\label{\\nogloxy{{$row[1]}}}
\\subsubsection{Informazioni generali}
END;
		if($row[2]!=null){
echo<<<END

\\begin{figure}[h]
\\centering
\\nogloxy{\\includegraphics[scale=0.4,keepaspectratio]{diagrammi/package/{{$row[2]}}.pdf}}
\\caption{\\nogloxy{{$row[1]}}}
\\end{figure}
\\FloatBarrier
END;
		}
echo<<<END

\\begin{itemize}
\\item \\textbf{Descrizione}\\\
$row[3]
END;
		if(($row[4]!=null) && ($row[5]!="Premi")){
echo<<<END

\\item \\textbf{Padre}: \\hyperref[\\nogloxy{{$row[5]}}]{\\nogloxy{\\texttt{{$row[6]}}}}
END;
		}
		$queryRelated="SELECT p.PrefixNome, p.Nome, p.Descrizione
					   FROM RelatedPackage rp JOIN Package p ON rp.Pack2=p.CodAuto
					   WHERE rp.Pack1='$row[0]'
					   UNION
					   SELECT p.PrefixNome, p.Nome, p.Descrizione
					   FROM RelatedPackage rp JOIN Package p ON rp.Pack1=p.CodAuto
					   WHERE rp.Pack2='$row[0]'
					   ORDER BY PrefixNome";
		$related=mysql_query($queryRelated,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga=mysql_fetch_row($related);
		if($riga[0]!=null){
echo<<<END

\\item \\textbf{Interazioni con altri componenti}:
\\begin{itemize}
\\item \\hyperref[\\nogloxy{{$riga[0]}}]{\\nogloxy{\\texttt{{$riga[1]}}}}\\\
$riga[2]
END;
			while($riga=mysql_fetch_row($related)){
echo<<<END

\\item \\hyperref[\\nogloxy{{$riga[0]}}]{\\nogloxy{\\texttt{{$riga[1]}}}}\\\
$riga[2]
END;
			}
echo<<<END

\\end{itemize}
END;
		}
		$querySubPack="SELECT p.PrefixNome, p.Nome, p.Descrizione
					   FROM Package p
					   WHERE p.Padre='$row[0]'
					   ORDER BY p.PrefixNome";
		$subpack=mysql_query($querySubPack,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga=mysql_fetch_row($subpack);
		if($riga[0]!=null){
echo<<<END

\\item \\textbf{Package contenuti}:
\\begin{itemize}
\\item \\hyperref[\\nogloxy{{$riga[0]}}]{\\nogloxy{\\texttt{{$riga[1]}}}}\\\
$riga[2]
END;
			while($riga=mysql_fetch_row($subpack)){
echo<<<END

\\item \\hyperref[\\nogloxy{{$riga[0]}}]{\\nogloxy{\\texttt{{$riga[1]}}}}\\\
$riga[2]
END;
			}
echo<<<END

\\end{itemize}
END;
		}
echo<<<END

\\end{itemize}
END;
		$queryClassi="SELECT c.CodAuto, c.PrefixNome, c.Descrizione, c.Utilizzo, c.UML
					  FROM Classe c
					  WHERE c.ContenutaIn='$row[0]'
					  ORDER BY c.PrefixNome";
		$classi=mysql_query($queryClassi,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga=mysql_fetch_row($classi);
		if($riga[0]!=null){
echo<<<END

\\subsubsection{Classi}
END;
			packageClassiCommonTex($conn, $riga, true);
		}
		else{
echo<<<END



END;
		}
		while($riga=mysql_fetch_row($classi)){
			packageClassiCommonTex($conn, $riga, true);
		}
	}
}
?>