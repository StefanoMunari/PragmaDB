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
	$query="SELECT r1.CodAuto,r1.IdRequisito,r1.Descrizione,r1.Tipo,r1.Importanza,r1.Padre,r1.Stato,r1.Soddisfatto,r1.Implementato,r1.Fonte, r2.IdRequisito, r2.Descrizione, f.IdFonte, f.Nome
			FROM (Requisiti r1 LEFT JOIN Requisiti r2 ON (r1.Padre=r2.CodAuto)) JOIN Fonti f ON r1.Fonte=f.CodAuto
			WHERE r1.CodAuto='$id'";
	$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($req);
	if($row[0]==$id){
		$title="Dettaglio Requisito - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
END;
		$heads=array('','IdRequisito:','Descrizione:','Tipo:','Importanza:');
		for($i=1;$i<5;$i++){
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
					<dd>$row[$i]</dd>
END;
		}
echo<<<END

					<dt class="widget-title">Padre:</dt>
END;
		if($row[10]!=null){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$row[5]">$row[10] - $row[11]</a></dd>
END;
		}
		else{
echo<<<END

					<dd>N/D</dd>
END;
		}
echo<<<END

					<dt class="widget-title">Stato:</dt>
END;
		if($row[4]!="Obbligatorio"){
			if($row[6]=="0"){
echo<<<END

					<dd class="mancante">Non Accettato</dd>
END;
			}
			else{
echo<<<END

					<dd class="completato">Accettato</dd>
END;
			}
		}
		else{
echo<<<END

					<dd>$row[4]</dd>
END;
		}
echo<<<END

					<dt class="widget-title">Soddisfatto:</dt>
END;
		if($row[7]=="0"){
echo<<<END

					<dd class="mancante">Non Soddisfatto</dd>
END;
		}
		else{
echo<<<END

					<dd class="completato">Soddisfatto</dd>
END;
		}
echo<<<END

					<dt class="widget-title">Implementato</dt>
END;
		if($row[8]=="0"){
echo<<<END

					<dd class="mancante">Non Implementato</dd>
END;
		}
		else{
echo<<<END

					<dd class="completato">Implementato</dd>
END;
		}
echo<<<END

					<dt class="widget-title">Fonte:</dt>
					<dd><a class="link-color-pers" href="$absurl/Fonti/dettagliofonte.php?id=$row[9]">$row[12] - $row[13]</a></dd>
END;
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto, r.IdRequisito, r.Descrizione
				FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
				WHERE r.Padre='$id'
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$sons=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($sons);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Figli:</dt>
					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($sons)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		//$query_ord="CALL sortForest('UseCase')";
		$query="SELECT u.CodAuto,u.IdUC, u.Nome
				FROM RequisitiUC ruc JOIN (_MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto) ON ruc.UC=u.CodAuto
				WHERE ruc.CodReq='$id'
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($uc);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Use Case Correlati:</dt>
					<dd><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($uc)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
echo<<<END

					<dt class="widget-title">Modifica Requisito:</dt>
					<dd><a class="link-color-pers" href="$absurl/Requisiti/modificarequisito.php?id=$id">Modifica $row[1]</a></dd>
				</dl>
END;
	}
	else{
		$title="Dettaglio Requisito - Requisito Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il requisito con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>