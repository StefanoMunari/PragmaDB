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
	$id=$_GET['id'];
	$id=mysql_escape_string($id);
	$conn=sql_conn();
	$queryTipo="SELECT t.Tipo
				FROM Test t
				WHERE t.CodAuto='$id'";
	$tipo=mysql_query($queryTipo,$conn) or fail("Query fallita: ".mysql_error($conn));
	$tipo=mysql_fetch_row($tipo);
	$tipo=$tipo[0];
	if($tipo=="Validazione"){
		$query="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Tipo, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Time, r.IdRequisito, r.CodAuto
				FROM Test t JOIN Requisiti r ON t.Requisito=r.CodAuto
				WHERE t.CodAuto='$id'";
	}
	elseif($tipo=="Sistema"){
		$query="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Tipo, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Time, r.IdRequisito, r.CodAuto
				FROM Test t JOIN Requisiti r ON t.Requisito=r.CodAuto
				WHERE t.CodAuto='$id'";
	}
	elseif($tipo=="Integrazione"){
		$query="SELECT t.CodAuto, t.IdTest, t.Tipo, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Time, p.PrefixNome, p.CodAuto
				FROM Test t JOIN Package p ON t.Package=p.CodAuto
				WHERE t.CodAuto='$id'";
	}
	else{
		$query="SELECT t.CodAuto, t.IdTest, t.Tipo, t.Descrizione, t.Implementato, t.Eseguito, t.Esito, t.Time
				FROM Test t
				WHERE t.CodAuto='$id'";
	}
	$test=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($test);
	if($row[0]==$id){
		$title="Dettaglio Test - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
END;
		//$heads: header dei campi di $row
		$heads=array('','IdTest:','Tipo:','Descrizione:','Implementato:','Eseguito:','Esito:','Time:');
		for($i=1;$i<4;$i++){
			//stampo il titolo del campo
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
END;
			if(($i==2) && ($row[$i]=="Unita")){
echo<<<END

					<dd>Unità</dd>
END;
			}
			else{
echo<<<END

					<dd>$row[$i]</dd>
END;
			}
		}
		$positive=array('','','','','Implementato','Eseguito','Superato');
		$negative=array('','','','','Non Implementato','Non Eseguito','Non Superato');
		for($i=4;$i<7;$i++){
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
END;
			if($row[$i]==0){
echo<<<END

					<dd class="mancante">$negative[$i]</dd>
END;
			}
			else{
echo<<<END

					<dd class="completato">$positive[$i]</dd>
END;
			}
		}
echo<<<END

					<dt class="widget-title">$heads[7]</dt>
					<dd>$row[7]</dd>
END;
		if(($row[2]=="Validazione") || ($row[2]=="Sistema")){
echo<<<END

					<dt class="widget-title">Requisito Oggetto:</dt>
					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$row[9]">$row[8]</a></dd>
END;
		}
		elseif($row[2]=="Integrazione"){
echo<<<END

					<dt class="widget-title">Package Oggetto:</dt>
					<dd><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$row[9]">$row[8]</a></dd>
END;
		}
		else{
			$query="SELECT m.CodAuto,m.AccessMod,m.Nome,m.ReturnType, c.PrefixNome, c.CodAuto
					FROM (TestMetodi tm JOIN Metodo m ON tm.CodMet=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
					WHERE tm.CodTest='$id'
					ORDER BY c.PrefixNome, m.Nome"; //Query che carica i metodi della classe
			$met=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END

					<dt class="widget-title">Metodi Oggetto:</dt>
END;
			while($riga = mysql_fetch_row($met)){
echo<<<END
					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga[5]">$riga[4]</a>   --->   $riga[1] <a class="link-color-pers" href="$absurl/Classi/Metodi/dettagliometodo.php?id=$riga[0]">$riga[2]</a>(
END;
				$query="SELECT p.CodAuto, p.Nome, p.Tipo
						FROM Parametro p
						WHERE p.Metodo=$riga[0]
						ORDER BY p.CodAuto";
				$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
				if($riga_par=mysql_fetch_row($par)){
echo<<<END
<a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga_par[0]">$riga_par[1]</a>: $riga_par[2]
END;
				}
				while($riga_par=mysql_fetch_row($par)){
echo<<<END
, <a class="link-color-pers" href="$absurl/Classi/Metodi/Parametri/dettaglioparametro.php?id=$riga_par[0]">$riga_par[1]</a>: $riga_par[2]
END;
				}
echo<<<END
)
END;
				if($riga[3]!=null){
echo<<<END
: $riga[3]
END;
				}
echo<<<END
</dd>
END;
			}
		}
		//------- Stampa il link per la modifica
echo<<<END

					<dt class="widget-title">Modifica Test:</dt>
					<dd><a class="link-color-pers" href="$absurl/Test/modificatest.php?id=$id">Modifica $row[1]</a></dd>
				</dl>
END;
	}
	else{
		//Non ho trovato niente con questo $id
		$title="Dettaglio Test - Test Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il test con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>