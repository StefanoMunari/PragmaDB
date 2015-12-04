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
	$queries[]="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Validazione' AND t.Eseguito='0'
				ORDER BY h.Position";
	$queries[]="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Validazione' AND t.Eseguito='1'
				ORDER BY h.Position";
	$tabletitle=array('Non Eseguiti','Eseguiti');
	$abbr=array('noneseguiti','eseguiti');
	$title="Dettaglio Metrica - Test di Validazione eseguiti";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Test di Validazione eseguiti</h2>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="#noneseguiti">Non Eseguiti</a></li>
							<li><a class="link-color-pers" href="#eseguiti">Eseguiti</a></li>
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
							<th>IdTest</th>
							<th>Descrizione</th>
							<th>Implementato</th>
							<th>Eseguito</th>
							<th>Esito</th>
							<th>Oggetto</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			test_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Test/modificatest.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Test/eliminatest.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
			while($row=mysql_fetch_row($ris)){
echo<<<END

						<tr>
END;
				test_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Test/modificatest.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Test/eliminatest.php?id=$row[0]">Elimina</a></li>
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