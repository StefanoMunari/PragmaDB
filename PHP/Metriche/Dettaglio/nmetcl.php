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
				FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
				GROUP BY m.Classe
				HAVING COUNT(*)>10
				ORDER BY COUNT(*) DESC, c.PrefixNome ASC";
	$queries[]="SELECT c.CodAuto,c.PrefixNome,c.Descrizione,COUNT(*),c.UML
				FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
				GROUP BY m.Classe
				HAVING COUNT(*)<11 AND COUNT(*)>7
				ORDER BY COUNT(*) DESC, c.PrefixNome ASC";
	$queries[]="SELECT c.CodAuto,c.PrefixNome,c.Descrizione,COUNT(*),c.UML
				FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
				GROUP BY m.Classe
				HAVING COUNT(*)<8
				ORDER BY COUNT(*) DESC, c.PrefixNome ASC";
	$tabletitle=array('Non Accettabili','Accettabili','Ottimali');
	$abbr=array('nonaccettabili','accettabili','ottimali');
	$title="Dettaglio Metrica - Numero di metodi per classe";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Numero di metodi per classe</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Valori di Riferimento</h4>
						<ul>
							<li><span class="mancante">Non Accettazione</span>: >=11</li>
							<li><span class="intermedio">Accettazione</span>: 1 - 10</li>
							<li><span class="completato">Ottimalità</span>: 1 - 7</li>
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
							<th>Metodi</th>
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
echo<<<END

			</div>
END;
	endpage_builder();
}
?>