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
	$query="SELECT a.CodAuto, a.Nome, a.Descrizione, a.Time
			FROM Attori a
			ORDER BY a.Nome";
	$att=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Attori";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Attori</h2>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Attori/inserisciattore.php">Inserisci Attore</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>Nome</th>
							<th>Descrizione</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
	while($row=mysql_fetch_row($att)){
echo<<<END

						<tr>
							<td><a class="link-color-pers" href="$absurl/Attori/dettaglioattore.php?id=$row[0]">$row[1]</a></td>
END;
		if($row[2]!=null){
echo<<<END

							<td>$row[2]</td>
END;
		}
		else{
echo<<<END

							<td></td>
END;
		}
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Attori/modificaattore.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Attori/eliminaattore.php?id=$row[0]">Elimina</a></li>
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