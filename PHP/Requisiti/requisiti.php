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
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query="SELECT r1.CodAuto,r1.IdRequisito,r1.Descrizione,r1.Tipo,r1.Importanza,r1.Padre,r1.Stato,r1.Soddisfatto,r1.Implementato,r1.Fonte,r2.IdRequisito,f.Nome
			FROM ((_MapRequisiti h JOIN Requisiti r1 ON h.CodAuto=r1.CodAuto) LEFT JOIN Requisiti r2 ON r1.Padre=r2.CodAuto) JOIN Fonti f ON r1.Fonte=f.CodAuto
			ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Requisiti";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Requisiti</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Esporta in LaTeX</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Requisiti/LaTeX/gettabellarequisiti.php">Tabella Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/Requisiti/LaTeX/gettracciamentorequisitifonti.php">Tracciamento Requisiti-Fonti</a></li>
							<li><a class="link-color-pers" href="$absurl/Requisiti/LaTeX/gettracciamentofontirequisiti.php">Tracciamento Fonti-Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/Requisiti/LaTeX/getriepilogorequisiti.php">Riepilogo Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/Requisiti/LaTeX/getrequisitiaccettati.php">Requisiti Accettati</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Requisiti/inseriscirequisito.php">Inserisci Requisito</a></li>
							<li><a class="link-color-pers" href="$absurl/Requisiti/requisitisolitari.php">Requisiti Solitari</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>IdRequisito</th>
							<th>Descrizione</th>
							<th>Tipo</th>
							<th>Importanza</th>
							<th>Padre</th>
							<th>Stato</th>
							<th>Soddisfatto</th>
							<th>Implementato</th>
							<th>Fonte</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
	while($row=mysql_fetch_row($req)){
echo<<<END

						<tr>
END;
		requisito_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Requisiti/storicorequisito.php?id=$row[0]">Storico</a></li>
									<li><a class="link-color-pers" href="$absurl/Requisiti/modificarequisito.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Requisiti/eliminarequisito.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
	}
echo<<<END

					</tbody>
				</table>
			</div>
END;
	endpage_builder();
}
?>