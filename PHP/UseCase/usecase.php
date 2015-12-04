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
	//$query_ord="CALL sortForest('UseCase')";
	$query="SELECT u1.CodAuto, u1.IdUC, u1.Nome, u1.Diagramma, u1.Descrizione, u1.Precondizioni, u1.Postcondizioni, u1.Padre, u1.ScenarioPrincipale, u1.Inclusioni, u1.Estensioni, u1.ScenariAlternativi, u1.Time, u2.IdUC
			FROM (_MapUseCase h JOIN UseCase u1 ON h.CodAuto=u1.CodAuto) LEFT JOIN UseCase u2 ON u1.Padre=u2.CodAuto
			ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$uc=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Use Case";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Use Case</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Esporta in LaTeX</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/UseCase/getusecase.php">Tabella Use Case</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/UseCase/inserisciusecase.php">Inserisci Use Case</a></li>
						</ul>
						<ul>
							<li><a class="link-color-pers" href="$absurl/UseCase/usecasesolitari.php">Use Case Solitari</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>IdUC</th>
							<th>Nome</th>
							<th>Diagramma</th>
							<th>Descrizione</th>
							<th>Precondizioni</th>
							<th>Postcondizioni</th>
							<th>Padre</th>
							<th>ScenarioPrincipale</th>
							<th>Inclusioni</th>
							<th>Estensioni</th>
							<th>ScenariAlternativi</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
	while($row=mysql_fetch_row($uc)){
echo<<<END

						<tr>
END;
		uc_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/UseCase/modificausecase.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/UseCase/eliminausecase.php?id=$row[0]">Elimina</a></li>
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