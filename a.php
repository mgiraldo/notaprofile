<?php
require_once('lib/class/NotAProfile.php');

if(isset($_GET["c"])){
	$cod = $_GET["c"];
	//Verifica que el código tenga la forma correcta
	//Asi se evitan ataques XSS
	if ( ereg ("^[0-9a-z]{32}$", $cod)){
		$resp = NotAProfile::activarUsuario($cod);	
		if($resp == 0){
			echo("Código de activación errado =)");
		}
		else{
			echo("Felicidades ".$resp." ahora eres un usuario activo de not_a_profile!");
		}
	}
	else{
		echo("Código de activación errado");
	}
	
} 
?>