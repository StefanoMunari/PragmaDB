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
	$conn=sql_conn();
	$nome=array("Requisiti Obbligatori Soddisfatti","Requisiti Accettati Soddisfatti","Requisiti Non Accettati Soddisfatti","SFIN - Ottimalità","SFOUT - Non Accettabilità","Metodi per classe - Non Accettabilità", "Parametri per metodo - Non Accettabilità","Componenti integrate","Test di Unità eseguiti","Test di Integrazione eseguiti","Test di Sistema eseguiti","Test di Validazione eseguiti","Test superati","Completezza Implementazione Funzionale","Densità di failure");
	$unita=array("%","%","%","%","%","%","%","%","%","%","%","%","%","%","%","%");
	$dettaglio=array("robsod","racsod","rnacsod","sfin","sfout","nmetcl","nparmet","comint","tuniese","tintese","tsissup","tvalese","denfailure","comimplfunz","denfailure");
	$value=array();
	$omin=array(100,100,50,50,0,0,0,100,100,80,80,100,100,100,0);
	$omax=array(100,100,100,9999999,3,5,3,100,100,100,100,100,100,100,0);
	$amin=array(100,100,0,30,0,0,0,100,90,70,70,100,90,100,0);
	$amax=array(100,100,100,9999999,6,15,5,100,100,100,100,100,100,100,10);
	//Requisiti Obbligatori Soddisfatti
	$query="SELECT COUNT(*) FROM Requisiti r WHERE r.Importanza='Obbligatorio' AND r.Soddisfatto='1'";
	$query_app="SELECT COUNT(*) FROM Requisiti r WHERE r.Importanza='Obbligatorio'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Requisiti Accettati Soddisfatti
	$query="SELECT COUNT(*) FROM Requisiti r WHERE r.Stato='1' AND r.Soddisfatto='1'";
	$query_app="SELECT COUNT(*) FROM Requisiti r WHERE r.Stato='1'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Requisiti Non Accettati Soddisfatti
	$query="SELECT COUNT(*) FROM Requisiti r WHERE r.Importanza<>'Obbligatorio' AND r.Stato='0' AND r.Soddisfatto='1'";
	$query_app="SELECT COUNT(*) FROM Requisiti r WHERE r.Importanza<>'Obbligatorio' AND r.Stato='0'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//SFIN - Ottimalita
	$query="SELECT COUNT(*) FROM Relazione r GROUP BY r.A HAVING COUNT(*)>1";
	$query_app="SELECT COUNT(*) FROM Classe c";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$i=0;
	while($ris=mysql_fetch_row($query)){
		$i++;
	}
	$num=mysql_fetch_row($query_app);
	$num=$num[0];
	$value[]=($i/$num)*100;
	//SFOUT - Non Accettabilita
	$query="SELECT COUNT(*) FROM Relazione r GROUP BY r.Da HAVING COUNT(*)>5";
	$query_app="SELECT COUNT(*) FROM Classe";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$i=0;
	while($ris=mysql_fetch_row($query)){
		$i++;
	}
	$num=mysql_fetch_row($query_app);
	$num=$num[0];
	$value[]=($i/$num)*100;
	//Metodi per classe - Non Accettabilità
	$query="SELECT COUNT(*) FROM Metodo m GROUP BY m.Classe HAVING COUNT(*)>10";
	$query_app="SELECT COUNT(*) FROM Classe";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$i=0;
	while($ris=mysql_fetch_row($query)){
		$i++;
	}
	$num=mysql_fetch_row($query_app);
	$num=$num[0];
	$value[]=($i/$num)*100;
	//Parametri per metodo - Non Accettabilità
	$query="SELECT COUNT(*) FROM Parametro p GROUP BY p.Metodo HAVING COUNT(*)>8";
	$query_app="SELECT COUNT(*) FROM Metodo";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$i=0;
	while($ris=mysql_fetch_row($query)){
		$i++;
	}
	$num=mysql_fetch_row($query_app);
	$num=$num[0];
	$value[]=($i/$num)*100;
	//Componenti integrate
	$query="SELECT DISTINCT COUNT(r.CodAuto) FROM RequisitiPackage rp JOIN Requisiti r ON rp.CodReq=r.CodAuto WHERE r.Soddisfatto='1' AND (r.Importanza='Obbligatorio' OR r.Stato='1')";
	$query_app="SELECT DISTINCT COUNT(r.CodAuto) FROM RequisitiPackage rp JOIN Requisiti r ON rp.CodReq=r.CodAuto WHERE r.Importanza='Obbligatorio' OR r.Stato='1'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Test di unità eseguiti
	$query="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Unita' AND t.Eseguito='1'";
	$query_app="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Unita'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Test di integrazione eseguiti
	$query="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Integrazione' AND t.Eseguito='1'";
	$query_app="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Integrazione'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Test di sistema eseguiti
	$query="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Sistema' AND t.Eseguito='1'";
	$query_app="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Sistema'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Test di validazione eseguiti
	$query="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Validazione' AND t.Eseguito='1'";
	$query_app="SELECT COUNT(*) FROM Test t WHERE t.Tipo='Validazione'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Test superati
	$query="SELECT COUNT(*) FROM Test t WHERE t.Esito='1'";
	$query_app="SELECT COUNT(*) FROM Test t WHERE t.Eseguito='1'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	if($query_app==0){
		$query_app=1;
	}
	$value[]=($query/$query_app)*100;
	//Completezza dell'implementazione funzionale
	$query="SELECT COUNT(*) FROM Requisiti r WHERE r.Tipo='Funzionale' AND (r.Importanza='Obbligatorio' OR r.Stato='1') AND r.Soddisfatto='1'";
	$query_app="SELECT COUNT(*) FROM Requisiti r WHERE r.Tipo='Funzionale' AND (r.Importanza='Obbligatorio' OR r.Stato='1')";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	$value[]=($query/$query_app)*100;
	//Densità di failure
	$query="SELECT COUNT(*) FROM Test t WHERE t.Eseguito='1' AND t.Esito='0'";
	$query_app="SELECT COUNT(*) FROM Test t WHERE t.Eseguito='1'";
	$query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query_app=mysql_query($query_app,$conn) or fail("Query fallita: ".mysql_error($conn));
	$query=mysql_fetch_row($query);
	$query=$query[0];
	$query_app=mysql_fetch_row($query_app);
	$query_app=$query_app[0];
	if($query_app==0){
		$query_app=1;
	}
	$value[]=($query/$query_app)*100;
	$title="Metriche";
	startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Metriche</h2>
				<table>
					<thead>
						<tr>
							<th>Metrica</th>
							<th>Unità di misura</th>
							<th>Valore</th>
							<th>Ottimalità</th>
							<th>Accettazione</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
	for($i=0;$i<15;$i++){
echo<<<END

						<tr>
							<td>$nome[$i]</td>
							<td>$unita[$i]</td>
END;
		$ottimo=true;
		$accettabile=true;
		if(($omin[$i]!=9999999) && ($value[$i]<$omin[$i])){
			$ottimo=false;
		}
		if(($omax[$i]!=9999999) && ($value[$i]>$omax[$i])){
			$ottimo=false;
		}
		if(($amin[$i]!=9999999) && ($value[$i]<$amin[$i])){
			$accettabile=false;
		}
		if(($amax[$i]!=9999999) && ($value[$i]>$amax[$i])){
			$accettabile=false;
		}
		if($ottimo==true){
echo<<<END

							<td class="completato">$value[$i]</td>
END;
		}
		elseif($accettabile==true){
echo<<<END

							<td class="intermedio">$value[$i]</td>
END;
		}
		else{
echo<<<END

							<td class="mancante">$value[$i]</td>
END;
		}
echo<<<END

							<td>
END;
		if($omin[$i]==9999999){
echo<<<END
<= $omax[$i]
END;
		}
		elseif($omax[$i]==9999999){
echo<<<END
>= $omin[$i]
END;
		}
		else{
echo<<<END
$omin[$i] - $omax[$i]
END;
		}
echo<<<END
</td>
							<td>
END;
		if($amin[$i]==9999999){
echo<<<END
<= $amax[$i]
END;
		}
		elseif($amax[$i]==9999999){
echo<<<END
>= $amin[$i]
END;
		}
		else{
echo<<<END
$amin[$i] - $amax[$i]
END;
		}
echo<<<END
</td>
							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Metriche/Dettaglio/$dettaglio[$i].php">Dettaglio</a></li>
								</ul>
							</td>
						</tr>
END;
	}
echo<<<END

					</tbody>
				</table>
			</div>
END;
	endpage_builder();
}
?>