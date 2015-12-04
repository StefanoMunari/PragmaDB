<?php

require('../Functions/urlLab.php');

$absurl=urlbasesito();

if($_GET["kThuGTf"]=="9Kd8fnYwcfHdieGmgklS"){
echo<<<END
<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="UTF-8" />
		<title>Form SuperUser - PragmaDB</title>
	</head>
	<body>
		<form action="$absurl/SuperUser/SUinsertRCor.php" id="formpage" method="post">
			<p>Inserire il testo completo da caricare:</p>
			<textarea rows="40" cols="180" id="page" name="page" form="formpage"></textarea>
			<input type="submit" id="submit" name="submit" value="Inserisci" />
		</form>
	</body>
</html>
END;
}
else{
	header("Location: $absurl/error.php");
}
?>