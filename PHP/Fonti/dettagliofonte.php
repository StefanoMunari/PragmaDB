<?php

require('../Functions/mysql_fun.php');
require('../Functions/page_builder.php');
require('../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	$id=$_GET['id'];
	$id=mysql_escape_string($id);
	$conn=sql_conn();
	$query="SELECT f.CodAuto, f.IdFonte, f.Nome, f.Descrizione, f.Time
			FROM Fonti f
			WHERE f.CodAuto='$id'";
	$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($req);
	if($row[0]==$id){
		$title="Dettaglio Fonte - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
END;
		$heads=array('','IdFonte:','Nome:','Descrizione:');
		for($i=1;$i<4;$i++){
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
END;
			if($row[$i]==null){
echo<<<END

					<dd>N/D</dd>
END;
			}
			else{
echo<<<END

					<dd>$row[$i]</dd>
END;
			}
		}
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto,r.IdRequisito, r.Descrizione
				FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
				WHERE r.Fonte='$id'
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$row = mysql_fetch_row($req);
		if($row[0]!=null){
echo<<<END

					<dt class="widget-title">Requisiti Correlati:</dt>
					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$row[0]">$row[1] - $row[2]</a></dd>
END;
		}
		while($row = mysql_fetch_row($req)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$row[0]">$row[1] - $row[2]</a></dd>
END;
		}
echo<<<END

				</dl>
END;
	}
	else{
		$title="Dettaglio Fonte - Fonte Non Trovata";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La fonte con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>