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
	$queries[]="SELECT m.CodAuto,CONCAT(c.PrefixNome,'::',m.Nome) AS Prefix,m.ReturnType,COUNT(*)
				FROM (Parametro p JOIN Metodo m ON p.Metodo=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
				GROUP BY p.Metodo
				HAVING COUNT(*)>8
				ORDER BY COUNT(*) DESC, Prefix ASC";
	$queries[]="SELECT m.CodAuto,CONCAT(c.PrefixNome,'::',m.Nome) AS Prefix,m.ReturnType,COUNT(*)
				FROM (Parametro p JOIN Metodo m ON p.Metodo=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
				GROUP BY p.Metodo
				HAVING COUNT(*)<9 AND COUNT(*)>4
				ORDER BY COUNT(*) DESC, Prefix ASC";
	$query0="SELECT m.CodAuto,CONCAT(c.PrefixNome,'::',m.Nome) AS Prefix,m.ReturnType,COUNT(*)
			 FROM (Parametro p JOIN Metodo m ON p.Metodo=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
			 GROUP BY p.Metodo
			 HAVING COUNT(*)<5
			 ORDER BY COUNT(*) DESC, Prefix ASC";
	$query1="SELECT m.CodAuto,CONCAT(c.PrefixNome,'::',m.Nome) AS Prefix,m.ReturnType,0
			 FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
			 WHERE m.CodAuto NOT IN (SELECT DISTINCT p.Metodo FROM Parametro p)
			 ORDER BY Prefix ASC";
	$tabletitle=array('Non Accettabili','Accettabili');
	$abbr=array('nonaccettabili','accettabili');
	$title="Dettaglio Metrica - Numero di parametri per metodo";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Numero di parametri per metodo</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Valori di Riferimento</h4>
						<ul>
							<li><span class="mancante">Non Accettazione</span>: >=9</li>
							<li><span class="intermedio">Accettazione</span>: 0 - 8</li>
							<li><span class="completato">Ottimalità</span>: 0 - 4</li>
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
							<th>Ritorno</th>
							<th>Parametri</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			metriche_metodi($row,$ind);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$row[0]">Parametri</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/modificametodo.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/eliminametodo.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
			while($row=mysql_fetch_row($ris)){
echo<<<END

						<tr>
END;
				metriche_metodi($row,$ind);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$row[0]">Parametri</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/modificametodo.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/eliminametodo.php?id=$row[0]">Elimina</a></li>
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
							<th>Ritorno</th>
							<th>Parametri</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
		metriche_metodi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$row[0]">Parametri</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/modificametodo.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/eliminametodo.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		while($row=mysql_fetch_row($ris0)){
echo<<<END

						<tr>
END;
			metriche_metodi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$row[0]">Parametri</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/modificametodo.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/eliminametodo.php?id=$row[0]">Elimina</a></li>
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
							<th>Ritorno</th>
							<th>Parametri</th>
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
		metriche_metodi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$row[0]">Parametri</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/modificametodo.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/eliminametodo.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		while($row=mysql_fetch_row($ris0)){
echo<<<END

						<tr>
END;
			metriche_metodi($row,2);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$row[0]">Parametri</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/modificametodo.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/eliminametodo.php?id=$row[0]">Elimina</a></li>
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