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
	$prova="SELECT r.CodAuto, r.IdRequisito
			FROM Requisiti r
			WHERE r.CodAuto='$id'";
	$prova=mysql_query($prova,$conn) or fail("Query fallita: ".mysql_error($conn));
	$prova=mysql_fetch_row($prova);
	if($prova[0]==$id){
		$query="SELECT re1.CodAuto,re1.IdRequisito,rt.Descrizione,rt.Tipo,rt.Importanza,rt.Padre,rt.Stato,rt.Soddisfatto,rt.Implementato,rt.Fonte,re2.IdRequisito,f.IdFonte,rt.IdTrack,u.Nome,u.Cognome,rt.Time
				FROM (((ReqTracking rt JOIN Requisiti re1 ON rt.CodAuto=re1.CodAuto) LEFT JOIN Requisiti re2 ON rt.Padre=re2.CodAuto) LEFT JOIN Fonti f ON rt.Fonte=f.CodAuto) JOIN Utenti u ON rt.Utente=u.Username
				WHERE rt.CodAuto='$id'
				ORDER BY rt.Time";
		$req=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$versione=0;
		$title="Storico Requisito - $prova[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Storico - $prova[1]</h2>
END;
		while($row=mysql_fetch_row($req)){
echo<<<END

				<h4 class="subtable-title">Versione $versione</h4>
				<table>
					<thead>
						<tr>
							<th>IdRequisito</th>
							<th>Descrizione</th>
							<th>Tipo</th>
							<th>Importanza</th>
							<th>Padre</th>
							<th>Stato</th>
							<th>Soddisfatto</th>
							<th>Implementato</th>
							<th>Fonte</th>
							<th>IdTrack</th>
							<th>Utente</th>
							<th>Time</th>
						</tr>
					</thead>
					<tbody>
						<tr>
END;
			requisito_table($row);
echo<<<END

							<td>$row[12]</td>
							<td>$row[13] $row[14]</td>
							<td>$row[15]</td>
						</tr>
					</tbody>
				</table>
END;
			$versione++;
		}
	}
	else{
		$title="Storico Requisito - Requisito Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il requisito con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>