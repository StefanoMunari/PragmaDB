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
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo
				FROM Test t
				WHERE t.Tipo='Unita' AND t.Eseguito='0'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo
				FROM Test t
				WHERE t.Tipo='Unita' AND t.Eseguito='1'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$tabletitle=array('Non Eseguiti','Eseguiti');
	$abbr=array('noneseguiti','eseguiti');
	$title="Dettaglio Metrica - Test di Unità eseguiti";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Test di Unità eseguiti</h2>
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