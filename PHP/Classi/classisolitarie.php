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
			WHERE c.CodAuto NOT IN (SELECT rc.CodClass FROM RequisitiClasse rc)
			ORDER BY c.PrefixNome";
	$cl=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Classi Solitarie";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Classi Solitarie</h2>
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