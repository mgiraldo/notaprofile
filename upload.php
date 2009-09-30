<?php

require_once('config/config.php');
require_once('lib/class/Viral.php');

if (array_key_exists("Filedata",$_FILES)) {
	echo (Viral::upload("Filedata"));
} else {
	echo ("error_nofiledata");
}

?>