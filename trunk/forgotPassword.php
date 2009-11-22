<?php 
require_once 'lib/class/NotAProfile.php';
global $app;

$msg = "Type your email. A code will be sended with a reactivation code.";

/* $cambiarClave:
 * 	   -1 :  Estado de Inicializacion o Error: No existe asociacion entre email y token ingresados
 * 		0 :  Erro cambiando clave  
 * 		1 :  Cambio de clave exitoso
 *      1 :  Envio de Email exitoso
 *   email:  Email del usuario cuando coincide el token
 */

if(isset($_POST['send'])){
	$exito = isset($_POST['email'])? NotAProfile::enviarEmailCambioClave($_POST['email']): false;
	$msg = $exito? "an email has just been sended with a reactivation code." : "You typed a non valid email.";
	$cambiarClave = $exito? 1:-1;
}
else if(isset($_POST['change'])&&isset($_POST['email2'])&&isset($_POST['pass2'])&&isset($_POST['pass3'])&&isset($_POST['act'])){

	$cambiarClave = NotAProfile::existeCambioClave($_GET['c']);
	$error = NotAProfile::cambiarClave($_POST['email2'],$_POST['pass2'],$_POST['pass3'],$_POST['act']);	
	switch($error){
	case 1:
		$msg = "<h2>Email or Password can not be empty </h2>";
		break;
	case 2:
		$msg = "<h2>Incorrect email format: ".$_POST['email2']."</h2>";
		break;
	case 3:
		$msg ="<h2>The passwords do not match!</h2>";
		break;
	case 4:
		$msg = "<h2>Email does not match activation code</h2>";
		break;
	case 5:
		$msg ="<h2>Error in Database procesing!! Contact webmaster righ now!!!!</h2>";
		break;	
	case 0:
		$msg = "<h2> Your password has been changed, you can now sign in again </h2>";
		$cambiarClave = 1;
		break;
	}
}
else if(isset($_GET['c'])){
	$msg = "Type and retype your new password.";	
	$cambiarClave = NotAProfile::existeCambioClave($_GET['c']);	
}
else{
	$cambiarClave = -1;
}




?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>not_a_profile</title>
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php
echo($msg);
if($cambiarClave==-1){  ?>
	
	<div id=forgot>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<label for="email">E-mail:</label>
			<input type="text" name="email" id="email" value="<?php echo isset($_POST['email'])?$_POST['email']:"";?>" tabindex="1" /><br/>
			<input type="submit" name="send" id="submit" value="Submit" tabindex="3" />
		</form>
	</div>
<?php  }

else if($cambiarClave==1){
	echo "<a href=".$app['url'].">GO</a>";
}

else {
?>
<div id=change>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']."".(isset($_GET['c'])?"?c=".$_GET['c']:""); ?>">
	<label for="pass2">Password:</label>
	<input type="password" name="pass2" id="pass2" value="" tabindex="5" /><br />
	<label for="pass3">Retype Password:</label>
	<input type="password" name="pass3" id="pass3" value="" tabindex="6" /><br />
	<input type="hidden" value="<?php echo($cambiarClave);?>" name="email2" tabindex="6" /><br />
	<input type="hidden" value="<?php echo($_GET['c']);?>" name="act" tabindex="6" /><br />
	<input type="submit" name="change" id="submit" value="Submit" tabindex="3" />
	</form>
</div>
<?php 
}
?>
</body>
</html>