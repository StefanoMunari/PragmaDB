<?php

require('../../Functions/mysql_fun.php');
require('../../Functions/page_builder.php');
require('../../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	$id=$_GET['id'];
	$id=mysql_escape_string($id);
	$conn=sql_conn();
	$query="SELECT a.CodAuto, a.AccessMod, a.Nome, a.Tipo, a.Descrizione, c.PrefixNome, a.Classe
			FROM Attributo a JOIN Classe c ON a.Classe=c.CodAuto
			WHERE a.CodAuto='$id'"; //query che carica l'attributo di id = $id
	$attr=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($attr);
	if($row[0]==$id){
		$title="Dettaglio Attributo - $row[2]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[2]</h2>
				<dl class="widget">
END;
		//$heads: header dei campi di $row
		$heads=array('','Accessibilità:','Nome:','Tipo:','Descrizione:','Classe:');
echo<<<END

					<dt class="widget-title">$heads[1]</dt>
END;
		if($row[1]=="-"){
echo<<<END

					<dd class="mancante">- (Private)</dd>
END;
		}
		elseif($row[1]=="#"){
echo<<<END

					<dd># (Protected)</dd>
END;
		}
		else{
echo<<<END

					<dd class="completato">+ (Public)</dd>
END;
		}
		for($i=2;$i<5;$i++){
			//stampo il titolo del campo
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
					<dd>$row[$i]</dd>
END;
		}
echo<<<END

					<dt class="widget-title">$heads[5]</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$row[6]">$row[5]</a></dd>
END;
		//------- Stampa il link per la modifica
echo<<<END

					<dt class="widget-title">Modifica Attributo:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/Attributi/modificaattributo.php?id=$id">Modifica $row[2]</a></dd>
				</dl>
END;
	}
	else{
		//Non ho trovato niente con questo $id
		$title="Dettaglio Attributo - Attributo Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>L'attributo con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>