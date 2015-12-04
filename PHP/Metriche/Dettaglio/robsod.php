<?php

require('../../Functions/mysql_fun.php');
require('../../Functions/page_builder.php');
require('../../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	//$query_ord="CALL sortForest('Requisiti')";
	$queries[]="SELECT r1.CodAuto,r1.IdRequisito,r1.Descrizione,r1.Tipo,r1.Importanza,r1.Padre,r1.Stato,r1.Soddisfatto,r1.Implementato,r1.Fonte,r2.IdRequisito,f.Nome
				FROM ((_MapRequisiti h JOIN Requisiti r1 ON h.CodAuto=r1.CodAuto) LEFT JOIN Requisiti r2 ON r1.Padre=r2.CodAuto) JOIN Fonti f ON r1.Fonte=f.CodAuto
				WHERE r1.Importanza='Obbligatorio' AND r1.Soddisfatto='0'
				ORDER BY h.Position";
	$queries[]="SELECT r1.CodAuto,r1.IdRequisito,r1.Descrizione,r1.Tipo,r1.Importanza,r1.Padre,r1.Stato,r1.Soddisfatto,r1.Implementato,r1.Fonte,r2.IdRequisito,f.Nome
				FROM ((_MapRequisiti h JOIN Requisiti r1 ON h.CodAuto=r1.CodAuto) LEFT JOIN Requisiti r2 ON r1.Padre=r2.CodAuto) JOIN Fonti f ON r1.Fonte=f.CodAuto
				WHERE r1.Importanza='Obbligatorio' AND r1.Soddisfatto='1'
				ORDER BY h.Position";
	$tabletitle=array('Non Soddisfatti','Soddisfatti');
	$abbr=array('nonsoddisfatti','soddisfatti');
	$title="Dettaglio Metrica - Requisiti Obbligatori Soddisfatti";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Requisiti Obbligatori Soddisfatti</h2>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="#nonsoddisfatti">Non Soddisfatti</a></li>
							<li><a class="link-color-pers" href="#soddisfatti">Soddisfatti</a></li>
						</ul>
					</aside>
				</div>
END;
	$conn=sql_conn();
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	foreach($queries as $ind => $query){
		$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		if($row=mysql_fetch_row($ris)){
echo<<<END

				<h4 id="$abbr[$ind]" class="subtable-title">$tabletitle[$ind]</h4>
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
			while($row=mysql_fetch_row($ris)){
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
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>