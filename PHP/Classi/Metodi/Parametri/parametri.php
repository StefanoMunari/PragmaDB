<?php

require('../../../Functions/mysql_fun.php');
require('../../../Functions/page_builder.php');
require('../../../Functions/urlLab.php');

session_start();
$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	$me=$_GET['me'];
	$me=mysql_escape_string($me);
	$conn=sql_conn();
	$query="SELECT m.CodAuto, m.Nome, m.Classe, c.PrefixNome
			FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
			WHERE m.CodAuto='$me'";
	$metodo=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row_met=mysql_fetch_row($metodo);
	if($row_met[0]==$me){
		$query="SELECT p.CodAuto, p.Nome, p.Tipo, p.Descrizione
				FROM Parametro p
				WHERE p.Metodo='$me'
				ORDER BY p.CodAuto";
		$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$title="$row_met[3] - $row_met[1] - Parametri";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$row_met[3] - $row_met[1] -  Parametri</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Link Utili</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row_met[2]">Torna a Tabella Metodi</a></li>
							<li><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Tabella Classi</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/inserisciparametro.php?me=$me">Inserisci Parametro</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>Nome</th>
							<th>Tipo</th>
							<th>Descrizione</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
		while($row=mysql_fetch_row($par)){
echo<<<END

						<tr>
END;
			par_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/modificaparametro.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/eliminaparametro.php?id=$row[0]">Elimina</a></li>
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
		$title="Parametri - Metodo Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il metodo con id "$me" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>