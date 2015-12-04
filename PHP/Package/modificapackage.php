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
	if(isset($_REQUEST['submit'])){
		$id=$_GET['id'];
		$old_nomef=$_POST["old_nome"];
		$nomef=$_POST["nome"];
		$old_prefixNomef=$_POST["old_prefixNome"];
		$old_descf=$_POST["old_desc"];
		$descf=$_POST["desc"];
		$old_padref=$_POST["old_padre"];
		$padref=$_POST["padre"];
		$old_diagf=$_POST["old_diag"];
		$diagf=$_POST["diag"];
		$prefixPadre="";
		$pkgf=""; //Package correlati
		$old_pkgf=$_POST["lista_old_pkg"];
		$old_pkg_array=explode(",", $old_pkgf);
		$num_pkgf=$_POST["num_pkg"];
		for($i=1;$i<=$num_pkgf;$i++){
			$temp=$_POST["pkg$i"];
			$pkgf="$pkgf"."$temp".",";
		}
		$pkg_array=explode(",", $pkgf);
		$modifica_pkg=false;
		foreach($pkg_array as $attuale){
			if(!(in_array($attuale, $old_pkg_array))){
				$modifica_pkg=true;
			}
		}
		foreach($old_pkg_array as $vecchio){
			if(!(in_array($vecchio, $pkg_array))){
				$modifica_pkg=true;
			}
		}
		/*$requif=""; //Package correlati
		$old_requif=$_POST["lista_old_requi"];
		$old_requi_array=explode(",", $old_requif);
		$num_requif=$_POST["num_requi"];
		for($i=1;$i<=$num_requif;$i++){
			$temp=$_POST["requi$i"];
			$requif="$requif"."$temp".",";
		}
		$requi_array=explode(",", $requif);*/
		$modifica_requi=false;
		/*foreach($requi_array as $attuale){
			if(!(in_array($attuale, $old_requi_array))){
				$modifica_requi=true;
			}
		}
		foreach($old_requi_array as $vecchio){
			if(!(in_array($vecchio, $requi_array))){
				$modifica_requi=true;
			}
		}*/
		$timestampf=$_POST["timestamp"];
		$err_no_modifica=false;
		$err_nome=false;
		$err_desc=false;
		$err_padre=false;
		$err_presente=false;
		$errori=0;
		if($old_padref==null){
			$old_padref="N/D";
		}
		if(($nomef==$old_nomef) && ($descf==$old_descf) && ($padref==$old_padref) && ($diagf==$old_diagf) && ($modifica_pkg==false) && ($modifica_requi==false)){
			$err_no_modifica=true;
			$errori++;
		}
		if($nomef==null){
			$err_nome=true;
			$errori++;
		}
		if($descf==null){
			$err_desc=true;
			$errori++;
		}
		if($padref!="N/D"){
			$conn=sql_conn();
			$query="SELECT p.RelationType,p.PrefixNome
					FROM Package p
					WHERE p.CodAuto='$padref'";
			$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$row=mysql_fetch_row($ris);
			if($row[0]==null){
				$err_padre=true;
				$errori++;
			}
			else{
				$prefixPadre="$row[1]::";
			}
		}
		$nomef=mysql_escape_string($nomef);
		$descf=mysql_escape_string($descf);
		$diagf=mysql_escape_string($diagf);
		if($old_prefixNomef!=($prefixPadre.$nomef)){
			$conn=sql_conn();
			$query="SELECT COUNT(*)
					FROM Package p
					WHERE p.PrefixNome='$prefixPadre"."$nomef'";
			$ris=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$row=mysql_fetch_row($ris);
			if($row[0]>0){
				$err_presente=true;
				$errori++;
			}
		}
		if($errori>0){
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento dei seguenti campi:</h2>
				<ul>
END;
			if($err_no_modifica){
echo<<<END

					<li>Nessun campo è stato modificato!</li>
END;
			}
			if($err_nome){
echo<<<END

					<li>Nome: NON INSERITO</li>
END;
			}
			if($err_desc){
echo<<<END

					<li>Descrizione: NON INSERITA</li>
END;
			}
			if($err_padre){
echo<<<END

					<li>Padre: IL PADRE INDICATO NON ESISTE</li>
END;
			}
			if($err_presente){
echo<<<END

					<li>IL PACKAGE E' GIA' PRESENTE NEL DB</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Package/modificapackage.php?id=$id">Riprova</a>.</p>
END;
		}
		else{
			$conn=sql_conn();
			$timestamp_query="SELECT p.Time
							  FROM Package p
							  WHERE p.CodAuto='$id'";
			$timestamp_query=mysql_query($timestamp_query,$conn) or fail("Query fallita: ".mysql_error($conn));
			if($row=mysql_fetch_row($timestamp_query)){
				$timestamp_db=$row[0];
				$timestamp_db=strtotime($timestamp_db);
				if($timestampf<$timestamp_db){
					$title="Errore";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>Il package è stato modificato da un altro utente; <a class="link-color-pers" href="$absurl/Package/modificapackage.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					if(($nomef!=$old_nomef) || ($descf!=$old_descf) || ($padref!=$old_padref) || ($diagf!=$old_diagf)){
						$query1="CALL modifyPackage('$id',";
						if($nomef==$old_nomef){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$nomef',";
						}
						if($old_prefixNomef==($prefixPadre.$nomef)){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$prefixPadre"."$nomef',";
						}
						if($descf==$old_descf){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$descf',";
						}
						if($diagf==$old_diagf){
							$query1=$query1."0,";
						}
						else{
							$query1=$query1."'$diagf',";
						}
						if($padref==$old_padref){
							$query1=$query1."0,";
						}
						elseif($padref=="N/D"){
							$query1=$query1."null,";
						}
						else{
							$query1=$query1."'$padref',";
						}
						$query1=$query1."'P')";
						$query1=mysql_query($query1,$conn) or fail("Query fallita: Modifica Package Fallita - ".mysql_error($conn));
					}
					if($modifica_pkg==true){
						if($num_pkgf>0){
							$query2="CALL modifyRelatedPackage('$id','$pkgf')";
						}
						else{
							$query2="CALL removeRelatedPackage('$id')";
						}
						$query2=mysql_query($query2,$conn) or fail("Query fallita: Modifica Package Correlati Fallita - ".mysql_error($conn));
					}
					/*if($modifica_requi==true){
						if($num_requif>0){
							$query3="CALL modifyPackageRequisiti('$id','$requif')";
						}
						else{
							$query3="CALL removePackageRequisiti('$id')";
						}
						$query3=mysql_query($query3,$conn) or fail("Query fallita: Inserimento Requisiti Correlati Fallito - ".mysql_error($conn));
					}*/
					$title="Package Modificato";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il package è stato modificato con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Package/package.php">Torna a Package</a>.</p>
END;
				}	
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>Il package è stato eliminato da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Package/package.php">Torna a Package</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
		$id=mysql_escape_string($id);
		$conn=sql_conn();
		$query="SELECT p1.CodAuto,p1.PrefixNome,p1.Nome,p1.Descrizione,p1.Padre,p1.UML
				FROM Package p1
				WHERE p1.CodAuto='$id'";
		$pack=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$timestamp=time();
		$packdb=mysql_fetch_row($pack);
		if($packdb[0]==$id){
			$title="Modifica Package - $packdb[1]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Modifica - $packdb[1]</h2>
				<div id="form">
					<form action="$absurl/Package/modificapackage.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="100" value="$packdb[2]" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$packdb[3]</textarea>
							</p>
							<p>
								<label for="padre">Padre:</label>
								<select id="padre" name="padre">
									<option value="N/D">N/D</option>
END;
			$conn=sql_conn();
			$query="SELECT p.CodAuto,p.PrefixNome
					FROM Package p
					ORDER BY p.PrefixNome"; //Query per recuperare l'id di tutti i package
					//in modo che $row[0] sia l'id e che $row[1] sia il [prefisso::]nome 
			$father=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			while($row=mysql_fetch_row($father)){
				if(($row[0]!=null) && ($row[0]!=$packdb[0])){
					if($row[0]==$packdb[4]){
echo<<<END

									<option value="$row[0]" selected="selected">$row[1]</option>
END;
					}
					else{
echo<<<END

									<option value="$row[0]">$row[1]</option>
END;
					}
				}
			}
echo<<<END

								</select>
							</p>
							<p>
								<label for="diag">Diagramma:</label>
								<input type="text" id="diag" name="diag" maxlength="50" value="$packdb[5]"/>
							</p>
							<script type="text/javascript" src="$absurl/UseCase/script_uc.js"></script>
							<p id="pkgs">
								<label for="pkg1">Componenti Correlati:</label>
END;
			$query="SELECT p.CodAuto,p.PrefixNome
					FROM Package p
					WHERE p.CodAuto<>'$id'
					ORDER BY p.PrefixNome";
			$tutti_pkg_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_pkg=array();
			while($row=mysql_fetch_row($tutti_pkg_query)){
				$tutti_pkg["$row[1]"]=$row[0];
			}
			$query="SELECT p.CodAuto,p.PrefixNome
					FROM RelatedPackage rp JOIN Package p ON rp.Pack2=p.CodAuto
					WHERE rp.Pack1='$id'
					UNION
					SELECT p.CodAuto,p.PrefixNome
					FROM RelatedPackage rp JOIN Package p ON rp.Pack1=p.CodAuto
					WHERE rp.Pack2='$id'
					ORDER BY PrefixNome";
			$pkg_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$pkg=array();
			$listaoldpkg="";
			$i=0;
			while($row=mysql_fetch_row($pkg_query)){
				$pkg["$row[1]"]=$row[0];
				$listaoldpkg=($listaoldpkg.$row[0]).",";
			}
			$pkg_rimanenti=array();
			foreach($tutti_pkg as $tiid => $tcod){
				$trovato=false;
				foreach($pkg as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
					$trovato=true;
					}
				}
				if($trovato==false){
					$pkg_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($pkg as $iid => $cod){
				$i++;
echo<<<END

								<select id="pkg$i" name="pkg$i" onchange="multiple_sel(4,$i)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($pkg_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$i++;
echo<<<END

								<select id="pkg$i" name="pkg$i" onchange="multiple_sel(4,$i)">
									<option value="N/D">N/D</option>
END;
			foreach($pkg_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
END;
/*
							<p id="requis">
								<label for="requi1">Requisiti Correlati:</label>
END;
			//$query_ord="CALL sortForest('Requisiti')";
			$query="SELECT r.CodAuto,r.IdRequisito
					FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
					ORDER BY h.Position";
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_requi_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$tutti_requi=array();
			while($row=mysql_fetch_row($tutti_requi_query)){
				$tutti_requi["$row[1]"]=$row[0];
			}
			//$query_update="CALL automatizeRequisitiPackage()";
			$query="SELECT r.CodAuto,r.IdRequisito
					FROM RequisitiPackage rp JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON rp.CodReq=r.CodAuto
					WHERE rp.CodPkg='$id'
					ORDER BY h.Position";
			//$upd=mysql_query($query_update,$conn) or fail("Query fallita: ".mysql_error($conn));
			$requi_query=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
			$requi=array();
			$listaoldrequi="";
			$j=0;
			while($row=mysql_fetch_row($requi_query)){
				$requi["$row[1]"]=$row[0];
				$listaoldrequi=($listaoldrequi.$row[0]).",";
			}
			$requi_rimanenti=array();
			foreach($tutti_requi as $tiid => $tcod){
				$trovato=false;
				foreach($requi as $conf_iid => $conf_cod){
					if($conf_cod==$tcod){
					$trovato=true;
					}
				}
				if($trovato==false){
					$requi_rimanenti["$tiid"]=$tcod;
				}
			}
			foreach($requi as $iid => $cod){
				$j++;
echo<<<END

								<select id="requi$j" name="requi$j" onchange="multiple_sel(2,$j)">
									<option value="N/D">N/D</option>
									<option value="$cod" selected="selected">$iid</option>
END;
				foreach($requi_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
				}
echo<<<END

								</select>
END;
			}
			$j++;
echo<<<END

								<select id="requi$j" name="requi$j" onchange="multiple_sel(2,$j)">
									<option value="N/D">N/D</option>
END;
			foreach($requi_rimanenti as $riid => $rcod){
echo<<<END

									<option value="$rcod">$riid</option>
END;
			}
echo<<<END

								</select>
							</p>
END;*/
			$i--;
			//$j--;
echo<<<END

							<input type="hidden" id="num_pkg" name="num_pkg" value="$i" />
END;
//							<input type="hidden" id="num_requi" name="num_requi" value="$j" />
echo<<<END

							<input type="hidden" id="old_prefixNome" name="old_prefixNome" value="$packdb[1]" />
							<input type="hidden" id="old_nome" name="old_nome" value="$packdb[2]" />
							<input type="hidden" id="old_desc" name="old_desc" value="$packdb[3]" />
							<input type="hidden" id="old_padre" name="old_padre" value="$packdb[4]" />
							<input type="hidden" id="old_diag" name="old_diag" value="$packdb[5]" />
							<input type="hidden" id="lista_old_pkg" name="lista_old_pkg" value="$listaoldpkg" />
END;
//							<input type="hidden" id="lista_old_requi" name="lista_old_requi" value="$listaoldrequi" />
echo<<<END

							<input type="hidden" id="timestamp" name="timestamp" value="$timestamp" />
							<p>
								<input type="submit" id="submit" name="submit" value="Modifica" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
END;
		}
		else{
			$title="Modifica Package - Package Non Trovato";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il package con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Package/package.php">Torna a Package</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>