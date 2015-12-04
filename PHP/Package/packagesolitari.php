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
	//$query_update="CALL automatizeRequisitiPackage()";
	$query="SELECT p1.CodAuto,p1.PrefixNome,p1.Nome,p1.Descrizione,p2.PrefixNome,p1.UML,p2.CodAuto
			FROM Package p1 LEFT JOIN Package p2 ON p1.Padre=p2.CodAuto
			WHERE p1.CodAuto NOT IN (SELECT rp.CodPkg FROM RequisitiPackage rp)
			ORDER BY p1.PrefixNome";
	//$upd=mysql_query($query_update,$conn) or fail("Query fallita: ".mysql_error($conn));
	$pack=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Package Solitari";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Package Solitari</h2>
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