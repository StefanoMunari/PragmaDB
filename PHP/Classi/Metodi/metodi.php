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
	$cl=$_GET['cl'];
	$cl=mysql_escape_string($cl);
	$conn=sql_conn();
	$query="SELECT c.CodAuto, c.PrefixNome
			FROM Classe c
			WHERE c.CodAuto='$cl'";
	$classe=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row_cl=mysql_fetch_row($classe);
	if($row_cl[0]==$cl){
		$query="SELECT m.CodAuto, m.AccessMod, m.Nome, m.ReturnType, m.Descrizione
				FROM Metodo m
				WHERE m.Classe='$cl'
				ORDER BY m.Nome";
		$met=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$title="$row_cl[1] - Metodi";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$row_cl[1] -  Metodi</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Link Utili</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Tabella Classi</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/Metodi/inseriscimetodo.php?cl=$cl">Inserisci Metodo</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>Accessibilità</th>
							<th>Nome</th>
							<th>Tipo Ritorno</th>
							<th>Descrizione</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
		while($row=mysql_fetch_row($met)){
echo<<<END

						<tr>
END;
			met_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$row[0]">Parametri</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/header.php?id=$row[0]">Header</a></li>
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
	else{
		$title="Metodi - Classe Non Trovata";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La classe con id "$cl" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>