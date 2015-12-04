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
	$id=$_GET['id'];
	$id=mysql_escape_string($id);
	$conn=sql_conn();
	$query="SELECT p.CodAuto, p.Nome, p.Tipo, p.Descrizione, m.Nome, p.Metodo, c.PrefixNome, c.CodAuto
			FROM (Parametro p JOIN Metodo m ON p.Metodo=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
			WHERE p.CodAuto='$id'"; //query che carica il parametro di id = $id
	$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($par);
	if($row[0]==$id){
		$title="Dettaglio Parametro - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
END;
		//$heads: header dei campi di $row
		$heads=array('','Nome:','Tipo:','Descrizione:','Metodo:');
		for($i=1;$i<4;$i++){
			//stampo il titolo del campo
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
					<dd>$row[$i]</dd>
END;
		}
echo<<<END

					<dt class="widget-title">$heads[4]</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/Metodi/dettagliometodo.php?id=$row[5]">$row[6] - $row[4]</a></dd>
END;
		//------- Stampa il link per la modifica
echo<<<END

					<dt class="widget-title">Modifica Parametro:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/modificaparametro.php?id=$id">Modifica $row[1]</a></dd>
				</dl>
END;
	}
	else{
		//Non ho trovato niente con questo $id
		$title="Dettaglio Parametro - Parametro Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il parametro con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>