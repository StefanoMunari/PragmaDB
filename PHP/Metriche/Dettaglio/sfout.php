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
	$queries[]="SELECT c.CodAuto,c.PrefixNome,c.Descrizione,COUNT(*),c.UML
				FROM Relazione r JOIN Classe c ON r.Da=c.CodAuto
				GROUP BY r.Da
				HAVING COUNT(*)>5
				ORDER BY COUNT(*) DESC, c.PrefixNome ASC";
	$queries[]="SELECT c.CodAuto,c.PrefixNome,c.Descrizione,COUNT(*),c.UML
				FROM Relazione r JOIN Classe c ON r.Da=c.CodAuto
				GROUP BY r.Da
				HAVING COUNT(*)>1 AND COUNT(*)<6
				ORDER BY COUNT(*) DESC, c.PrefixNome ASC";
	$query0="SELECT c.CodAuto,c.PrefixNome,c.Descrizione,COUNT(*),c.UML
			 FROM Relazione r JOIN Classe c ON r.Da=c.CodAuto
			 GROUP BY r.Da
			 HAVING COUNT(*)<2
			 ORDER BY COUNT(*) DESC, c.PrefixNome ASC";
	$query1="SELECT c.CodAuto,c.PrefixNome,c.Descrizione,0,c.UML
			 FROM Classe c
			 WHERE c.CodAuto NOT IN (SELECT r.Da FROM Relazione r)
			 ORDER BY c.PrefixNome";
	$tabletitle=array('Non Accettabili','Accettabili');
	$abbr=array('nonaccettabili','accettabili');
	$title="Dettaglio Metrica - Structural Fan-Out";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Structural Fan-Out</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Valori di Riferimento</h4>
						<ul>
							<li><span class="mancante">Non Accettazione</span>: >=6</li>
							<li><span class="intermedio">Accettazione</span>: 0 - 5</li>
							<li><span class="completato">Ottimalità</span>: 0 - 1</li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="#nonaccettabili">Non Accettabili</a></li>
							<li><a class="link-color-pers" href="#accettabili">Accettabili</a></li>
							<li><a class="link-color-pers" href="#ottimali">Ottimali</a></li>
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
							<th>PrefixNome</th>
							<th>Descrizione</th>
							<th>Relazioni OUT</th>
							<th>Diagramma</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			metriche_classi($row,$ind);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$row[0]">Attributi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[0]">Metodi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/eliminaclasse.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
			while($row=mysql_fetch_row($ris)){
echo<<<END

						<tr>
END;
				metriche_classi($row,$ind);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$row[0]">Attributi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[0]">Metodi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/eliminaclasse.php?id=$row[0]">Elimina</a></li>
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
	$ris0=mysql_query($query0,$conn) or fail("Query fallita: ".mysql_error($conn));
	$found=false;
	if($row=mysql_fetch_row($ris0)){
		$found=true;
echo<<<END

				<h4 id="ottimali" class="subtable-title">Ottimali</h4>
				<table>
					<thead>
						<tr>
							<th>PrefixNome</th>
							<th>Descrizione</th>
							<th>Relazioni OUT</th>
							<th>Diagramma</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
		metriche_classi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$row[0]">Attributi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[0]">Metodi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/eliminaclasse.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		while($row=mysql_fetch_row($ris0)){
echo<<<END

						<tr>
END;
			metriche_classi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$row[0]">Attributi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[0]">Metodi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/eliminaclasse.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		}
	}
	$ris1=mysql_query($query1,$conn) or fail("Query fallita: ".mysql_error($conn));
	if($row=mysql_fetch_row($ris1)){
		if($found==false){
echo<<<END

				<h4 id="ottimali" class="subtable-title">Ottimali</h4>
				<table>
					<thead>
						<tr>
							<th>PrefixNome</th>
							<th>Descrizione</th>
							<th>Relazioni OUT</th>
							<th>Diagramma</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
		}
		$found=true;
echo<<<END
						<tr>
END;
		metriche_classi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$row[0]">Attributi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[0]">Metodi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/eliminaclasse.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		while($row=mysql_fetch_row($ris1)){
echo<<<END

						<tr>
END;
			metriche_classi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$row[0]">Attributi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[0]">Metodi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/eliminaclasse.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		}
	}
	if($found==true){
echo<<<END

					</tbody>
				</table>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>