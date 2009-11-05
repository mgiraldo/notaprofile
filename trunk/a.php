<?php
require_once('lib/class/NotAProfile.php');

if(isset($_GET["c"])){
	$cod = $_GET["c"];
	if ( ereg ("^[0-9a-z]{32}$", $cod)){
		NotAProfile::activarUsuario($cod);	
	}
	else{
		echo("Código de activación errado");
	}
	
} 
?>