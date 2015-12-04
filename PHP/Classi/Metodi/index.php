<?php

require('../../Functions/urlLab.php');

$absurl=urlbasesito();

echo<<<END
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="refresh" content="0; url=$absurl/Classi/classi.php">
		<title>Redirecting...</title>
	</head>
	<body></body>
</html>
END;

?>