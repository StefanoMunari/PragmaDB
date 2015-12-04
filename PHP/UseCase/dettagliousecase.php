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
	$query="SELECT u1.CodAuto, u1.IdUC, u1.Nome, u1.Diagramma, u1.Descrizione, u1.Precondizioni, u1.Postcondizioni, u1.Padre, u1.ScenarioPrincipale, u1.Inclusioni, u1.Estensioni, u1.ScenariAlternativi, u1.Time, u2.IdUC, u2.Nome
			FROM UseCase u1 LEFT JOIN UseCase u2 ON u1.Padre=u2.CodAuto
			WHERE u1.CodAuto='$id'";
	$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($uc);
	if($row[0]==$id){
		$title="Dettaglio Use Case - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
END;
		$heads=array('','IdUseCase:','Nome:','PercorsoDiagramma:','Descrizione:','Precondizioni:','Postcondizioni:','Padre:','ScenarioPrincipale:','Inclusioni:','Estensioni:','ScenariAlternativi','Time:');
		for($i=1;$i<13;$i++){
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
END;
			if($row[$i]!=null){
				if($i==7){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$row[7]">$row[13] - $row[14]</a></dd>
END;
				}
				else{
echo<<<END

					<dd>$row[$i]</dd>
END;
				}
			}
			else{
echo<<<END

					<dd>N/D</dd>
END;
			}
		}
		$query="SELECT a.CodAuto, a.Nome
				FROM AttoriUC auc JOIN Attori a ON auc.Attore=a.CodAuto
				WHERE auc.UC='$id'
				ORDER BY a.Nome";
		$att=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($att);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Attori:</dt>
					<dd><a class="link-color-pers" href="$absurl/Attori/dettaglioattore.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($att)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Attori/dettaglioattore.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		//$query_ord="CALL sortForest('UseCase')";
		$query="SELECT u.CodAuto, u.IdUC, u.Nome
				FROM _MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto
				WHERE u.Padre='$id'
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$sons=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($sons);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Figli:</dt>
					<dd><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($sons)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto, r.IdRequisito, r.Descrizione
				FROM RequisitiUC ruc JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON ruc.CodReq=r.CodAuto
				WHERE ruc.UC='$id'
				ORDER BY h.Position";
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($req);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Requisiti Correlati:</dt>
					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($req)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
echo<<<END

					<dt class="widget-title">Modifica Use Case:</dt>
					<dd><a class="link-color-pers" href="$absurl/UseCase/modificausecase.php?id=$id">Modifica $row[1]</a></dd>
				</dl>
END;
	}
	else{
		$title="Dettaglio Use Case - Use Case Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Lo use case con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>