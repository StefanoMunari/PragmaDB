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
	$query="SELECT c.CodAuto,c.PrefixNome,c.Nome,c.Descrizione,c.Utilizzo,p.PrefixNome,c.UML,c.Time,p.CodAuto
			FROM Classe c JOIN Package p ON c.ContenutaIn=p.CodAuto
			WHERE c.CodAuto='$id'"; //query che carica la classe di id = $id
	$cl=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$row=mysql_fetch_row($cl);
	if($row[0]==$id){
		$title="Dettaglio Classe - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
END;
		//$heads: header dei campi di $row
		$heads=array('','PrefixNome:','Nome:','Descrizione:','Utilizzo','ContenutaIn:','Diagramma:','Time:');
		for($i=1;$i<8;$i++){
			//stampo il titolo del campo
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
END;
			if($row[$i]!=null){
				//se non è nullo
				if($i==5){
					//se è il nome del padre
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$row[8]">$row[$i]</a></dd>
END;
				}
				else{
echo<<<END

					<dd>$row[$i]</dd>
END;
				}
			}
			else{
				//altrimenti stampo N/D
echo<<<END

					<dd>N/D</dd>
END;
			}
		}
		//------- Stampa attributi
		$query="SELECT a.CodAuto,a.AccessMod,a.Nome,a.Tipo
				FROM Attributo a
				WHERE a.Classe='$id'
				ORDER BY a.Nome"; //Query che carica gli attributi della classe
		$attr=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($attr);
		//Stampa il link per la gestione
echo<<<END

					<dt class="widget-title">Attributi:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$id">GESTISCI</a></dd>
END;
		if($riga[0]!=null){
echo<<<END
					<dd>$riga[1] <a class="link-color-pers" href="$absurl/Classi/Attributi/dettaglioattributo.php?id=$riga[0]">$riga[2]</a>: $riga[3]</dd>
END;
		}
		while($riga = mysql_fetch_row($attr)){
echo<<<END

					<dd>$riga[1] <a class="link-color-pers" href="$absurl/Classi/Attributi/dettaglioattributo.php?id=$riga[0]">$riga[2]</a>: $riga[3]</dd>
END;
		}
		//------- Stampa metodi
		$query="SELECT m.CodAuto,m.AccessMod,m.Nome,m.ReturnType
				FROM Metodo m
				WHERE m.Classe='$id'
				ORDER BY m.Nome"; //Query che carica i metodi della classe
		$met=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($met);
		//Stampa il link per la gestione
echo<<<END

					<dt class="widget-title">Metodi:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$id">GESTISCI</a></dd>
END;
		if($riga[0]!=null){
echo<<<END
					<dd>$riga[1] <a class="link-color-pers" href="$absurl/Classi/Metodi/dettagliometodo.php?id=$riga[0]">$riga[2]</a>(
END;
			$conn=sql_conn();
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
		while($riga = mysql_fetch_row($met)){
echo<<<END

					<dd>$riga[1] <a class="link-color-pers" href="$absurl/Classi/Metodi/dettagliometodo.php?id=$riga[0]">$riga[2]</a>(
END;
			$conn=sql_conn();
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
		//------- Stampa classi da cui eredita
		$query="SELECT c1.CodAuto,c1.PrefixNome
				FROM EreditaDa ed JOIN Classe c1 ON ed.Padre=c1.CodAuto
				WHERE ed.Figlio='$id'
				ORDER BY c1.PrefixNome"; //Query che carica i le classi da cui eredita id
		$padri=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($padri);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Eredita da:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($padri)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		//------- Stampa classi che da lei ereditano
		$query="SELECT c2.CodAuto,c2.PrefixNome
				FROM EreditaDa ed JOIN Classe c2 ON ed.Figlio=c2.CodAuto
				WHERE ed.Padre='$id'
				ORDER BY c2.PrefixNome"; //Query che carica le classi contenute nel package $id
		$figli=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($figli);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Ereditano da lei:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($figli)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		//------- Stampa le relazioni con le altre classi
		$query1="SELECT c1.CodAuto,c1.PrefixNome
				FROM Relazione r JOIN Classe c1 ON r.Da=c1.CodAuto
				WHERE r.A='$id'
				ORDER BY c1.PrefixNome";
		$query2="SELECT c2.CodAuto,c2.PrefixNome
				FROM Relazione r JOIN Classe c2 ON r.A=c2.CodAuto
				WHERE r.Da='$id'
				ORDER BY c2.PrefixNome"; //Query che carica l'id e il nome delle classi che sono in relazione tra loro. Occhio che deve considerare i casi che
		//la classe corrente sia a destra sia a sinistra
		$relcl1=mysql_query($query1,$conn) or fail("Query fallita: ".mysql_error($conn));
		$relcl2=mysql_query($query2,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga1 = mysql_fetch_row($relcl1);
		$riga2 = mysql_fetch_row($relcl2);
		if(($riga1[0]!=null)||($riga2[0]!=null)){
echo<<<END

					<dt class="widget-title">Classi Correlate:</dt>
END;
		}
		if($riga1[0]!=null){
echo<<<END

					<dd>IN - <a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga1[0]">$riga1[1]</a></dd>
END;
		}
		while($riga1 = mysql_fetch_row($relcl1)){
echo<<<END

					<dd>IN - <a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga1[0]">$riga1[1]</a></dd>
END;
		}
		if($riga2[0]!=null){
echo<<<END

					<dd>OUT - <a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga2[0]">$riga2[1]</a></dd>
END;
		}
		while($riga2 = mysql_fetch_row($relcl2)){
echo<<<END

					<dd>OUT - <a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga2[0]">$riga2[1]</a></dd>
END;
		}
		//------- Stampa i requisiti correlati
		//$query_ord="CALL sortForest('Requisiti')";
		$query="SELECT r.CodAuto, r.IdRequisito, r.Descrizione
				FROM RequisitiClasse rc JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON rc.CodReq=r.CodAuto
				WHERE rc.CodClass='$id'
				ORDER BY h.Position"; //Query che carica i requisiti correlati
		//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
		$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$riga = mysql_fetch_row($req);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Requisiti Correlati:</dt>
					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		while($riga = mysql_fetch_row($req)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		//------- Stampa il link per la modifica
echo<<<END

					<dt class="widget-title">Modifica Classe:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/modificaclasse.php?id=$id">Modifica $row[1]</a></dd>
				</dl>
END;
	}
	else{
		//Non ho trovato niente con questo $id
		$title="Dettaglio Classe - Classe Non Trovata";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La classe con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>