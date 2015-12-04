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
	$query="SELECT m.CodAuto, m.AccessMod, m.Nome, m.ReturnType, m.Descrizione, c.PrefixNome, m.Classe
			FROM Metodo m JOIN Classe c ON m.Classe=c.CodAuto
			WHERE m.CodAuto='$id'"; //query che carica il metodo di id = $id
	$met=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($met);
	if($row[0]==$id){
		$title="Dettaglio Metodo - $row[2]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[2]</h2>
				<dl class="widget">
END;
		//$heads: header dei campi di $row
		$heads=array('','Accessibilità:','Nome:','Tipo Ritorno:','Descrizione:','Classe:');
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
END;
			if($row[$i]!=null){
echo<<<END

					<dd>$row[$i]</dd>
END;
			}
			else{
echo<<<END

					<dd>N/D</dd>
END;
			}
		}
echo<<<END

					<dt class="widget-title">$heads[5]</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$row[6]">$row[5]</a></dd>
END;
		//------- Stampa parametri
		$query="SELECT p.CodAuto, p.Nome, p.Tipo
				FROM Parametro p
				WHERE p.Metodo='$id'
				ORDER BY p.CodAuto"; //Query che carica i parametri del metodo
		$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($par);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Parametri:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/parametri.php?me=$id">GESTISCI</a></dd>
					<dd><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga[0]">$riga[1]</a>: $riga[2]</dd>
END;
		}
		while($riga = mysql_fetch_row($par)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga[0]">$riga[1]</a>: $riga[2]</dd>
END;
		}
		//------- Stampa il link per la modifica
echo<<<END

					<dt class="widget-title">Modifica Metodo:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/Metodi/modificametodo.php?id=$id">Modifica $row[2]</a></dd>
				</dl>
END;
	}
	else{
		//Non ho trovato niente con questo $id
		$title="Dettaglio Metodo - Metodo Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il metodo con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>