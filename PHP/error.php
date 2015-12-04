<?php

require('Functions/page_builder.php');
require('Functions/urlLab.php');

$absurl=urlbasesito();

$title="Autenticazione Fallita";
startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<p>L'autenticazione a PragmaDB è fallita. <a class="link-color-pers" href="$absurl/index.php">Riprova</a>.</p>
			</div>
END;
endpage_builder();
?>