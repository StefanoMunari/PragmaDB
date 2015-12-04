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
	$query="SELECT LEFT(g.IdTermine,1)
			FROM Glossario g
			WHERE (CONVERT(RIGHT(g.IdTermine,LENGTH(g.IdTermine)-1),UNSIGNED INT) = 1)
			ORDER BY LEFT(g.IdTermine,1),CONVERT(SUBSTRING(g.IdTermine,2),UNSIGNED INT)";
	$menu=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$title="Glossario";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Glossario</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Esporta in LaTeX</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Glossario/getglossario.php">Glossario</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Glossario/inseriscitermine.php">Inserisci Termine</a></li>
						</ul>
					</aside>
				</div>
				<div class="letter-navigation">
					<ul>
END;
	$alphas=range('A', 'Z');
	$letter=mysql_fetch_row($menu);
	foreach($alphas AS $alpha){
		if(lcfirst($alpha)==$letter[0]){
echo<<<END

						<li><a class="link-color-pers" href="#$alpha">$alpha</a></li>
END;
			$letter=mysql_fetch_row($menu);
		}
		else{
echo<<<END

						<li>$alpha</li>
END;
		}
	}
echo<<<END

					</ul>
				</div>
END;
	$query="SELECT g.CodAuto, g.IdTermine, g.Identificativo, g.Name, g.Description, g.First, g.FirstPlural, g.Text, g.Plural, g.Time, CONVERT(RIGHT(g.IdTermine,LENGTH(g.IdTermine)-1),UNSIGNED INT)
			FROM Glossario g
			ORDER BY LEFT(g.IdTermine,1),CONVERT(SUBSTRING(g.IdTermine,2),UNSIGNED INT)";
	$glo=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	if($row=mysql_fetch_row($glo)){
		if($row[10]==1){
			$id=$row[1];
			$startletter=ucfirst($id[0]);
echo<<<END

				<h4 class="subtable-title">$startletter</h4>
				<table>
					<thead id="$startletter">
						<tr>
							<th>Identificativo</th>
							<th>Name</th>
							<th>Description</th>
							<th>First</th>
							<th>FirstPlural</th>
							<th>Text</th>
							<th>Plural</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
		}
echo<<<END

						<tr>
END;
		for($i=2;$i<9;$i++){
			if($row[$i]!=null){
echo<<<END

							<td>$row[$i]</td>
END;
			}
			else{
echo<<<END

							<td></td>
END;
			}
		}
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Glossario/modificatermine.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Glossario/eliminatermine.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		while($row=mysql_fetch_row($glo)){
			if($row[10]==1){
				$id=$row[1];
				$startletter=ucfirst($id[0]);
echo<<<END

					</tbody>
				</table>
				<h4 class="subtable-title">$startletter</h4>
				<table id="$startletter">
					<thead>
						<tr>
							<th>Identificativo</th>
							<th>Name</th>
							<th>Description</th>
							<th>First</th>
							<th>FirstPlural</th>
							<th>Text</th>
							<th>Plural</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
			}
echo<<<END

						<tr>
END;
			for($i=2;$i<9;$i++){
				if($row[$i]!=null){
echo<<<END

							<td>$row[$i]</td>
END;
				}
				else{
echo<<<END

							<td></td>
END;
				}
			}
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Glossario/modificatermine.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Glossario/eliminatermine.php?id=$row[0]">Elimina</a></li>
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
echo<<<END

			</div>
END;
	endpage_builder();
}
?>