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
	
	$query="SELECT p1.CodAuto,p1.PrefixNome,p1.Nome,p1.Descrizione,p2.PrefixNome,p1.UML,p2.CodAuto
			FROM Package p1 LEFT JOIN Package p2 ON p1.Padre=p2.CodAuto
			ORDER BY p1.PrefixNome";
	$pack=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Package";
	startpage_builder($title);
/*							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/getpackageclassistbe.php">Package/Classi ST (Back-End)</a></li>
							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/getpackageclassistfe.php">Package/Classi ST (Front-End)</a></li>*/
echo<<<END

			<div id="content">
				<h2>Package</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Esporta in LaTeX (DP)</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/getpackageclassidpbe.php">Package/Classi DP (Back-End)</a></li>
							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/getpackageclassidpfe.php">Package/Classi DP (Front-End)</a></li>
							<li><a class="link-color-pers" href="$absurl/Classi/LaTeX/gettracciamentoclassirequisiti.php">Tracciamento Classi-Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/Classi/LaTeX/gettracciamentorequisiticlassi.php">Tracciamento Requisiti-Classi</a></li>
							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/gettracciamentocomponentirequisiti.php">Tracciamento Componenti-Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/gettracciamentorequisiticomponenti.php">Tracciamento Requisiti-Componenti</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Package/inseriscipackage.php">Inserisci Package</a></li>
							<li><a class="link-color-pers" href="$absurl/Package/packagesolitari.php">Package Solitari</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>PrefixNome</th>
							<th>Nome</th>
							<th>Descrizione</th>
							<th>Padre</th>
							<th>Diagramma</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
	while($row=mysql_fetch_row($pack)){
echo<<<END

						<tr>
END;
		package_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Package/modificapackage.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Package/eliminapackage.php?id=$row[0]">Elimina</a></li>
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