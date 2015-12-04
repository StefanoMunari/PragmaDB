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
	$tipi=array('Validazione','Sistema','Integrazione','Unità');
	$abbr=array('validazione','sistema','integrazione','unita');
	//$query_ord="CALL sortForest('Requisiti')";
	$queries[]="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Validazione'
				ORDER BY h.Position";
	$queries[]="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Sistema'
				ORDER BY h.Position";
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, p.PrefixNome, p.CodAuto
				FROM Test t JOIN Package p ON t.Package=p.CodAuto
				WHERE t.Tipo='Integrazione'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo
				FROM Test t
				WHERE t.Tipo='Unita'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$title="Test";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Test</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Esporta in LaTeX</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettabelletest.php">Tabelle Test</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentoTVrequisiti.php">Tracciamento TV-Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentorequisitiTV.php">Tracciamento Requisiti-TV</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentoTSrequisiti.php">Tracciamento TS-Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentorequisitiTS.php">Tracciamento Requisiti-TS</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentoTIcomponenti.php">Tracciamento TI-Package</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentocomponentiTI.php">Tracciamento Package-TI</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentoTUmetodi.php">Tracciamento TU-Metodi</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/LaTeX/gettracciamentometodiTU.php">Tracciamento Metodi-TU</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="#validazione">Test di Validazione</a></li>
							<li><a class="link-color-pers" href="#sistema">Test di Sistema</a></li>
							<li><a class="link-color-pers" href="#integrazione">Test di Integrazione</a></li>
							<li><a class="link-color-pers" href="#unita">Test di Unità</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/inseriscitest.php">Inserisci Test</a></li>
						</ul>
					</aside>
				</div>
END;
	$conn=sql_conn();
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	foreach($queries as $ind => $query){
		$test=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		if($row=mysql_fetch_row($test)){
echo<<<END

				<h4 id="$abbr[$ind]" class="subtable-title">Test di $tipi[$ind]</h4>
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
			while($row=mysql_fetch_row($test)){
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