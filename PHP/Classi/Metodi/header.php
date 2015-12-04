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
		$title="Header Metodo - $row[2]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$row[5]::$row[2]</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Link Utili - 1</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/Metodi/metodi.php?cl=$row[6]">Torna a Tabella Metodi</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Link Utili - 2</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Tabella Classi</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget">
					<h4 class="widget-title">Header</h4>
					<code>
						/**<br />
END;
		if(substr_count($row[2], "\\")>0){
echo<<<END

                 		<span class="mancante">* @name $row[2]</span><br />
END;
		}
		else{
echo<<<END

                 		* @name $row[2]<br />
END;
		}
		if(substr_count($row[4], "\\")>0){
echo<<<END

                 		<span class="mancante">* @desc $row[4]</span><br />
END;
		}
		else{
echo<<<END

                 		* @desc $row[4]<br />
END;
		}
		//------- Stampa parametri
		$query="SELECT p.CodAuto, p.Nome, p.Tipo, p.Descrizione
				FROM Parametro p
				WHERE p.Metodo='$id'
				ORDER BY p.CodAuto"; //Query che carica i parametri del metodo
		$par=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		while($riga = mysql_fetch_row($par)){
			if((substr_count($riga[2], "\\")>0)||(substr_count($riga[1], "\\")>0)||(substr_count($riga[3], "\\")>0)){
echo<<<END

                 		<span class="mancante">* @param {{$riga[2]}} $riga[1] - $riga[3]</span><br />
END;
			}
			else{
echo<<<END

                 		* @param {{$riga[2]}} $riga[1] - $riga[3]<br />
END;
			}
		}
		$classe=str_replace("::", ".", $row[5]);
		if($row[3]!=null){
			if(substr_count($row[3], "\\")>0){
echo<<<END

                 		<span class="mancante">* @returns {{$row[3]}}<br />
END;
			}
			else{
echo<<<END

                 		* @returns {{$row[3]}}<br />
END;
			}
		}
		if(substr_count($classe, "\\")>0){
echo<<<END

                 		<span class="mancante">* @memberOf $classe<br />
END;
		}
		else{
echo<<<END

                 		* @memberOf $classe<br />
END;
		}
echo<<<END

                 		*/
                 	</code>
                 </div>
END;
	}
	else{
		//Non ho trovato niente con questo $id
		$title="Header Metodo - Metodo Non Trovato";
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