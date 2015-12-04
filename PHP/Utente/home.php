<?php

require('../Functions/page_builder.php');
require('../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	$title="Homepage";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Ciao $_SESSION[nome], benvenuto in PragmaDB!</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="sections" class="widget">
						<h4 class="widget-title">Sezioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Attori/attori.php">Attori</a></li>
							<li><a class="link-color-pers" href="$absurl/Classi/classi.php">Classi</a></li>
							<li><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Fonti</a></li>
							<li><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Glossario</a></li>
							<li><a class="link-color-pers" href="$absurl/Metriche/metriche.php">Metriche</a></li>
							<li><a class="link-color-pers" href="$absurl/Package/package.php">Package</a></li>
							<li><a class="link-color-pers" href="$absurl/Test/test.php">Test</a></li>
							<li><a class="link-color-pers" href="$absurl/Requisiti/requisiti.php">Requisiti</a></li>
							<li><a class="link-color-pers" href="$absurl/UseCase/usecase.php">Use Case</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="links" class="widget">
						<h4 class="widget-title">Link Utili</h4>
						<ul>
							<li><a class="link-color-pers" href="https://bitbucket.org/">Bitbucket</a></li>
							<li><a class="link-color-pers" href="">Comandi Custom LaTeX</a></li>
							<li><a class="link-color-pers" href="https://www.hostedredmine.com">Hosted Redmine</a></li>
							<li><a class="link-color-pers" href="https://groups.yahoo.com/neo">Yahoo! Groups</a></li>
						</ul>
					</aside>
				</div>
			</div>
END;
	endpage_builder();
}
?>
