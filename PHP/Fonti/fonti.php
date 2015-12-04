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
	$query="SELECT f.CodAuto, f.IdFonte, f.Nome, f.Descrizione, f.Time
			FROM Fonti f
			ORDER BY f.IdFonte";
	$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Fonti";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Fonti</h2>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Fonti/inseriscifonte.php">Inserisci Fonte</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>IdFonte</th>
							<th>Nome</th>
							<th>Descrizione</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
	while($row=mysql_fetch_row($req)){
echo<<<END

						<tr>
							<td><a class="link-color-pers" href="$absurl/Fonti/dettagliofonte.php?id=$row[0]">$row[1]</a></td>
END;
		for($i=2;$i<4;$i++){
echo<<<END

							<td>$row[$i]</td>
END;
		}
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Fonti/modificafonte.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Fonti/eliminafonte.php?id=$row[0]">Elimina</a></li>
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