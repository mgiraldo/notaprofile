<?php

require_once('config/config.php');
require_once('lib/class/Viral.php');

if (array_key_exists("email",$_POST)) {
	echo (Viral::send($_POST["name"],$_POST["email"],$_POST["friends"],$_POST["photo"],$_POST["setting"],$_POST["character"],$_POST["song"],$_POST["message"],$_POST["id"]));
} else {
	echo ("error_nodata");
}

?>