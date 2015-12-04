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
	$queries_nsup[]="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
					 FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
					 WHERE t.Tipo='Validazione' AND t.Eseguito='1' AND t.Esito='0'
					 ORDER BY h.Position";
	$queries_nsup[]="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
					 FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
					 WHERE t.Tipo='Sistema' AND t.Eseguito='1' AND t.Esito='0'
					 ORDER BY h.Position";
	$queries_nsup[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, p.PrefixNome, p.CodAuto
					 FROM Test t JOIN Package p ON t.Package=p.CodAuto
					 WHERE t.Tipo='Integrazione' AND t.Eseguito='1' AND t.Esito='0'
					 ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$queries_nsup[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo
					 FROM Test t
					 WHERE t.Tipo='Unita' AND t.Eseguito='1' AND t.Esito='0'
					 ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$queries_sup[]="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
					FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
					WHERE t.Tipo='Validazione' AND t.Esito='1'
					ORDER BY h.Position";
	$queries_sup[]="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, r.IdRequisito, r.CodAuto
					FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
					WHERE t.Tipo='Sistema' AND t.Esito='1'
					ORDER BY h.Position";
	$queries_sup[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo, p.PrefixNome, p.CodAuto
					FROM Test t JOIN Package p ON t.Package=p.CodAuto
					WHERE t.Tipo='Integrazione' AND t.Esito='1'
					ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$queries_sup[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Tipo
					FROM Test t
					WHERE t.Tipo='Unita' AND t.Esito='1'
					ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$tabletitle=array('Validazione','Sistema','Integrazione','Unità');
	$abbr=array('validazione','sistema','integrazione','unita');
	$title="Dettaglio Metrica - Densità di failure";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Densità di failure - Test Eseguiti Non Superati</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title"><a class="link-color-pers" href="#nonsuperati">Non Superati</a></h4>
						<ul>
							<li><a class="link-color-pers" href="#nonsuperativalidazione">Validazione</a></li>
							<li><a class="link-color-pers" href="#nonsuperatisistema">Sistema</a></li>
							<li><a class="link-color-pers" href="#nonsuperatiintegrazione">Integrazione</a></li>
							<li><a class="link-color-pers" href="#nonsuperatiunita">Unità</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title"><a class="link-color-pers" href="#superati">Superati</a></h4>
						<ul>
							<li><a class="link-color-pers" href="#superativalidazione">Validazione</a></li>
							<li><a class="link-color-pers" href="#superatisistema">Sistema</a></li>
							<li><a class="link-color-pers" href="#superatiintegrazione">Integrazione</a></li>
							<li><a class="link-color-pers" href="#superatiunita">Unità</a></li>
						</ul>
					</aside>
				</div>
END;
	$conn=sql_conn();
	$first=true;
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	foreach($queries_nsup as $ind => $query){
		$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		if($row=mysql_fetch_row($ris)){
			if($first){
echo<<<END

				<h4 id="nonsuperati" class="subtable-title">Non Superati</h4>
END;
			}
			$first=false;
echo<<<END

				<h4 id="nonsuperati$abbr[$ind]" class="subtable-title">$tabletitle[$ind]</h4>
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
	$first=true;
	foreach($queries_sup as $ind => $query){
		$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		if($row=mysql_fetch_row($ris)){
			if($first){
echo<<<END

				<h4 id="superati" class="subtable-title">Superati</h4>
END;
			}
			$first=false;
echo<<<END

				<h4 id="superati$abbr[$ind]" class="subtable-title">$tabletitle[$ind]</h4>
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