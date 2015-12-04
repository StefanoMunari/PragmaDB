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
	
	$query="SELECT c.CodAuto,c.PrefixNome,c.Nome,c.Descrizione,c.Utilizzo,p.PrefixNome,c.UML,p.CodAuto
			FROM Classe c JOIN Package p ON c.ContenutaIn=p.CodAuto
			ORDER BY c.PrefixNome";
	$cl=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Classi";
	startpage_builder($title);
/*							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/getpackageclassistbe.php">Package/Classi ST (Back-End)</a></li>
							<li><a class="link-color-pers" href="$absurl/Package/LaTeX/getpackageclassistfe.php">Package/Classi ST (Front-End)</a></li>*/
echo<<<END

			<div id="content">
				<h2>Classi</h2>
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
							<li><a class="link-color-pers" href="$absurl/Classi/inserisciclasse.php">Inserisci Classe</a></li>
							<li><a class="link-color-pers" href="$absurl/Classi/classisolitarie.php">Classi Solitarie</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>PrefixNome</th>
							<th>Nome</th>
							<th>Descrizione</th>
							<th>Utilizzo</th>
							<th>ContenutaIn</th>
							<th>Diagramma</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
	while($row=mysql_fetch_row($cl)){
echo<<<END

						<tr>
END;
		class_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$row[0]">Attributi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[0]">Metodi</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/idrequisiticorrelati.php?id=$row[0]">Requisiti</a></li>
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
			</div>
END;
	endpage_builder();
}
?>