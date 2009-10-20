<?php
/**
 * Pagina Inicial del Proyecto
 */
require_once 'lib/class/NotAProfile.php';

// Declaramos las varfiables que vamos a usar en el formulario para prevenir XSS por URL
$email = "";
$pass = "";
$pass2 = "";

//Esta variable se encarga de mostrar un error en pantalla si es necesario
$msgError = 0;

//Verificamos primer si se está enviando el formulario
if(isset($_POST['email'])){
		// Se esta enviando formulario para login
		$email = trim("".$_POST['email']);
		if($email == ""){$msgError = 1;}
		else{
			$regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|museum|name))/";
			if(preg_match($regex,$email)){
				$email = strip_tags($email);
				$email = addslashes(htmlspecialchars(htmlentities($email)));
				
				if(isset($_POST['pass'])){
					// En este momento se realiza la validación de las credenciales introducidas
					$pass = trim("".$_POST['pass']);
					if($pass == ""){$msgError = 2;}
					else{
					  header('Location:http://localhost/not_a_profile/visualizacionLlaves.php');	
					}
					
					
					
				}else {
					$msgError = 2;
				}
			}
			else{
				$msgError = 1;
			}
		}
		
	
		
}else if(isset($_POST['email2'])){
		// Se esta enviando formulario para registrarse
		
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
<h1>not_a_profile</h1>

<?php 

switch($msgError){
	case 0:
		echo "<h1>No error</h1>";
		break;
	case 1:
		echo "<h1>Debe ingresar una direccion de correo o un formato valido</h1>"; 
		break;
	case 2:
		echo "<h1>Debe ingresar una constrasena </h1>";
		break;
	case 4: 
		echo "<h1>Termino ok </h1>";
		break;
	default:
		echo "wtf?";
		break;
}

?>
<p> Formulario de prueba para ingreso al sistema: </p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="email">E-mail:</label>
	<input type="text" name="email" id="email" value="" tabindex="1" /><br />
	<label for="pass">Password:</label>
	<input type="text" name="pass" id="pass" value="" tabindex="2" /><br />
	<input type="submit" name="submit" id="Submit" value="Submit" tabindex="3" />
</form>
<hr></hr>
<p> Formulario de prueba para registro: </p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="email2">E-mail:</label>
	<input type="text" name="email2" id="email2" value="" tabindex="4" /><br />
	<label for="pass2">Contrasena:</label>
	<input type="text" name="pass2" id="pass2" value="" tabindex="5" /><br />
	<label for="pass3">Confirmar Contrasena:</label>
	<input type="text" name="pass3" id="pass3" value="" tabindex="6" /><br />
	<input type="submit" name="registrar" id="registrar" value="Registrarse" tabindex="7" />
</form>
</body>
</html>
 
 