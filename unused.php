<?php

require_once('config/config.php');
require_once('lib/class/Llave.php');

$sinusar = Llave::getUnused();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>not_a_profile</title>
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>not_a_profile</h1>
<?php if (count($sinusar)>0) { ?>
<ul>
<?php
	for ($i=0;$i<count($sinusar);++$i) {
		$row = $sinusar[$i];
?>
	<li><?php echo $row["id"]?></li>
<?php } ?>
</ul>
<?php } ?>
</body>
</html>