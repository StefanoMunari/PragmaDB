<?php

function startpage_builder($title){
	$absurl=urlbasesito();
echo<<<END
<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="UTF-8" />
		<meta content="width=device-width, initial-scale=1" name="viewport" />
		<link rel="stylesheet" type="text/css" media="all" href="$absurl/style.css" />
		<link rel="icon" type="img/ico" href="$absurl/Immagini/favicon.ico" />
		<title>$title - PragmaDB</title>
	</head>
	<body>
		<div id="container">
END;
	if(($_SERVER['PHP_SELF']=="$absurl/index.php") || empty($_SESSION['user'])){
echo<<<END

			<header class="site-header index-head" role="banner">
END;
	}
	else{
echo<<<END

			<header class="site-header" role="banner">
END;
	}
echo<<<END

				<div class="site-branding header-color-pers">
					<h1 class="site-title">PragmaDB</h1>
					<h2 class="site-description">The Pragma Team DB System</h2>
				</div>
END;
	if(($_SERVER['PHP_SELF']!="$absurl/index.php") && !empty($_SESSION['user'])){
echo<<<END

				<nav id="site-navigation" class="main-navigation" role="navigation">
					<div>
						<ul>
							<li class="page_item"><a class="link-color-pers" href="$absurl/Utente/home.php">Homepage</a></li>
							<li class="page_item">
								<a class="link-color-pers" href="#">Sezioni</a>
								<ul class="children">
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Attori/attori.php">Attori</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Classi/classi.php">Classi</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Fonti/fonti.php">Fonti</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Glossario/glossario.php">Glossario</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Metriche/metriche.php">Metriche</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Package/package.php">Package</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Requisiti</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/Test/test.php">Test</a>
									</li>
									<li class="page_item">
										<a class="link-color-pers" href="$absurl/UseCase/usecase.php">Use Case</a>
									</li>
								</ul>
							</li>
							<li class="page_item"><a class="link-color-pers" href="$absurl/Utente/changepass.php">Cambia Password</a></li>
							<li class="page_item"><a class="link-color-pers" href="$absurl/Utente/logout.php">Logout</a></li>
						</ul>
					</div>
				</nav>
END;
	}
echo<<<END

			</header>
END;
}

function requisito_table($row){
	$absurl=urlbasesito();
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$row[0]">$row[1]</a></td>
END;
		for($i=2;$i<5;$i++){
echo<<<END

							<td>$row[$i]</td>
END;
		}
		if($row[10]!=null){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$row[5]">$row[10]</a></td>
END;
		}
		else{
echo<<<END

							<td></td>
END;
		}
		if($row[4]!="Obbligatorio"){
			if($row[6]==0){
echo<<<END

							<td class="mancante">Non Accettato</td>
END;
			}
			else{
echo<<<END

							<td class="completato">Accettato</td>
END;
			}
		}
		else{
echo<<<END

							<td>$row[4]</td>
END;
		}
		if($row[7]==0){
echo<<<END

							<td class="mancante">Non Soddisfatto</td>
END;
		}
		else{
echo<<<END

							<td class="completato">Soddisfatto</td>
END;
		}
		if($row[8]==0){
echo<<<END

							<td class="mancante">Non Implementato</td>
END;
		}
		else{
echo<<<END

							<td class="completato">Implementato</td>
END;
		}
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Fonti/dettagliofonte.php?id=$row[9]">$row[11]</a></td>
END;
}

function uc_table($row){
	$absurl=urlbasesito();
echo<<<END

							<td><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$row[0]">$row[1]</a></td>
END;
		for($i=2;$i<12;$i++){
			if($row[$i]!=null){
				if($i==7){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$row[7]">$row[13]</a></td>
END;
				}
				else{
echo<<<END

							<td>$row[$i]</td>
END;
				}
			}
			else{
echo<<<END

							<td></td>
END;
			}
		}
}


function package_table($row){
	$absurl=urlbasesito();
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$row[0]">$row[1]</a></td>
END;
	for($i=2;$i<6;$i++){
		if($row[$i]!=null){
			if($i==4){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$row[6]">$row[$i]</a></td>
END;
			}
			else{
echo<<<END

							<td>$row[$i]</td>
END;
			}
		}
		else{
echo<<<END

							<td></td>
END;
		}
	}
}

function class_table($row){
	$absurl=urlbasesito();
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$row[0]">$row[1]</a></td>
END;
	for($i=2;$i<7;$i++){
		if($row[$i]!=null){
			if($i==5){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$row[7]">$row[$i]</a></td>
END;
			}
			else{
echo<<<END

							<td>$row[$i]</td>
END;
			}
		}
		else{
echo<<<END

							<td></td>
END;
		}
	}
}

function attr_table($row){
	$absurl=urlbasesito();
	for($i=1;$i<5;$i++){
		if($row[$i]!=null){
			if($i==2){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Classi/Attributi/dettaglioattributo.php?id=$row[0]">$row[2]</a></td>
END;
			}
			else{
echo<<<END

							<td>$row[$i]</td>
END;
			}
		}
		else{
echo<<<END

							<td></td>
END;
		}
	}
}

function met_table($row){
	$absurl=urlbasesito();
	for($i=1;$i<5;$i++){
		if($row[$i]!=null){
			if($i==2){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Classi/Metodi/dettagliometodo.php?id=$row[0]">$row[2]</a>(
END;
				$conn=sql_conn();
				$query="SELECT p.CodAuto, p.Nome, p.Tipo
						FROM Parametro p
						WHERE p.Metodo=$row[0]
						ORDER BY p.CodAuto";
				$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				if($riga=mysql_fetch_row($par)){
echo<<<END
<a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga[0]">$riga[1]</a>: $riga[2]
END;
				}
				while($riga=mysql_fetch_row($par)){
echo<<<END
, <a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga[0]">$riga[1]</a>: $riga[2]
END;
				}
echo<<<END
)</td>
END;
			}
			else{
echo<<<END

							<td>$row[$i]</td>
END;
			}
		}
		else{
echo<<<END

							<td></td>
END;
		}
	}
}

function metriche_classi($row,$met){
	$absurl=urlbasesito();
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$row[0]">$row[1]</a></td>
END;
	for($i=2;$i<5;$i++){
		if($row[$i]!=null){
			if($i==3){
				if($met==0){
echo<<<END

							<td class="mancante">$row[$i]</td>
END;
				}
				elseif($met==1){
echo<<<END

							<td class="intermedio">$row[$i]</td>
END;
				}
				else{
echo<<<END

							<td class="completato">$row[$i]</td>
END;
				}
			}
			else{
echo<<<END

							<td>$row[$i]</td>
END;
			}
		}
		else{
echo<<<END

							<td></td>
END;
		}
	}
}

function metriche_metodi($row,$met){
	$absurl=urlbasesito();
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Classi/Metodi/dettagliometodo.php?id=$row[0]">$row[1]</a>(
END;
				$conn=sql_conn();
				$query="SELECT p.CodAuto, p.Nome, p.Tipo
						FROM Parametro p
						WHERE p.Metodo=$row[0]
						ORDER BY p.CodAuto";
				$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				if($riga=mysql_fetch_row($par)){
echo<<<END
<a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga[0]">$riga[1]</a>: $riga[2]
END;
				}
				while($riga=mysql_fetch_row($par)){
echo<<<END
, <a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga[0]">$riga[1]</a>: $riga[2]
END;
				}
echo<<<END
)</td>
END;
	for($i=2;$i<4;$i++){
		if($row[$i]!=null){
			if($i==3){
				if($met==0){
echo<<<END

							<td class="mancante">$row[$i]</td>
END;
				}
				elseif($met==1){
echo<<<END

							<td class="intermedio">$row[$i]</td>
END;
				}
				else{
echo<<<END

							<td class="completato">$row[$i]</td>
END;
				}
			}
			else{
echo<<<END

							<td>$row[$i]</td>
END;
			}
		}
		else{
echo<<<END

							<td></td>
END;
		}
	}
}

function par_table($row){
	$absurl=urlbasesito();
	for($i=1;$i<4;$i++){
		if($row[$i]!=null){
			if($i==1){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$row[0]">$row[1]</a></td>
END;
			}
			else{
echo<<<END

							<td>$row[$i]</td>
END;
			}
		}
		else{
echo<<<END

							<td></td>
END;
		}
	}
}

function test_table($row){
	$absurl=urlbasesito();
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Test/dettagliotest.php?id=$row[0]">$row[1]</a></td>
							<td>$row[2]</td>
END;
	$positive=array('','','','Implementato','Eseguito','Superato');
	$negative=array('','','','Non Implementato','Non Eseguito','Non Superato');
	for($i=3;$i<6;$i++){
		if($row[$i]==0){
echo<<<END

							<td class="mancante">$negative[$i]</td>
END;
		}
		else{
echo<<<END

							<td class="completato">$positive[$i]</td>
END;
		}
	}
	if(($row[6]=="Validazione") || ($row[6]=="Sistema")){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$row[8]">$row[7]</a></td>
END;
	}
	elseif($row[6]=="Integrazione"){
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$row[8]">$row[7]</a></td>
END;
	}
	else{
echo<<<END

							<td><a class="link-color-pers" href="$absurl/Test/dettagliotest.php?id=$row[0]">Vedi dettaglio</a></td>
END;
	}
}

function endpage_builder(){
	$absurl=urlbasesito();
echo<<<END

			<div id="push"></div>
		</div>
END;
	if(($_SERVER['PHP_SELF']!="$absurl/index.php") && !empty($_SESSION['user'])){
echo<<<END

		<footer class="site-footer footer-color-pers" role="contentinfo">
			<div class="site-info">
				<p>Pragma</p>
			</div>
		</footer>
		<p id="backTop" class="back-top">
			<a rel="nofollow" class="back-top-a-color-pers" href="#top"></a>
		</p>
		<script type="text/javascript" src="$absurl/script.js"></script>
END;
	}
echo<<<END

	</body>
</html>
END;
}

?>