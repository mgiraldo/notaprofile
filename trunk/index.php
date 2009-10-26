<?php
/**
 * Pagina Inicial del Proyecto
 */
require_once 'lib/class/NotAProfile.php';
if($usuario->logged_in){
	header("Location: ./home.php");
}else{

// Declaramos las varfiables que vamos a usar en el formulario para prevenir XSS por URL
$email = "";
$pass = "";
$pass2 = "";

//Esta variable se encarga de mostrar un error en pantalla si es necesario
$msgError = "";

if(isset($_POST['submit'])){
	// Se ha enviado el formulario para iniciar sesion
	
	$email = trim("".$_POST['email']);
	if($email == ""){$msgError = $msgError."Email cannot be empty";}
	else{
		$regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|museum|name))/";
		if(preg_match($regex,$email)){
			$email = strip_tags($email);
			$email = addslashes(htmlspecialchars(htmlentities($email)));
				
			if(isset($_POST['pass'])){
				$pass = trim("".$_POST['pass']);
				if($pass == ""){$msgError = $msgError."The password cannot be empty";}
				else{
					$pass = strip_tags($pass);
					$pass = addslashes(htmlspecialchars(htmlentities($pass)));
					
					// En este momento tenemos el email y el password escritos de manera adecuada
					$retval = $usuario->login($email, $pass);	
					if($retval){
						header("Location: ./home.php");
					}
					/* Login failed */
					else{
						header("Location: ./index.php");
			      	}		
				}
			}
		}
		else{$msgError = $msgError."Incorrect Email format";}
	}
	
}else if(isset($_POST['signup'])){
	// Se ha enviado el formulario para registrarse
	
	$email = trim("".$_POST['email2']);
	if($email == ""){$msgError = $msgError."Email cannot be empty";}
	else{
		$regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|museum|name))/";
		if(preg_match($regex,$email)){
			$email = strip_tags($email);
			$email = addslashes(htmlspecialchars(htmlentities($email)));
				
			if(isset($_POST['pass2'])){
				if(isset($_POST['pass3'])){
					$pass = trim("".$_POST['pass2']);
					$pass2 = trim("".$_POST['pass3']);
					if($pass == ""){$msgError = $msgError."The password cannot be empty";}
					else{
						if($pass == $pass2){
							$pass = strip_tags($pass);
							$pass = addslashes(htmlspecialchars(htmlentities($pass)));
		
							// En este momento tenemos el email y el password escritos de manera adecuada
							//NotAProfile::RegistrarUsuario($email, $pass);
							if(true){
								header('Location:'.$app['url'].'test');
							}
						}else{$msgError = $msgError."Your password doesnt match";}
					}
				}
			}
		}
		else{$msgError = $msgError."Incorrect Email format";}
	}
	
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
echo "<h1>".$msgError."</h1>";
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="email">E-mail:</label>
	<input type="text" name="email" id="email" value="" tabindex="1" /><br />
	<label for="pass">Password:</label>
	<input type="text" name="pass" id="pass" value="" tabindex="2" /><br />
	<input type="submit" name="submit" id="submit" value="Submit" tabindex="3" />
</form>
<hr></hr>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="email2">E-mail:</label>
	<input type="text" name="email2" id="email2" value="" tabindex="4" /><br />
	<label for="pass2">Contrasena:</label>
	<input type="text" name="pass2" id="pass2" value="" tabindex="5" /><br />
	<label for="pass3">Confirmar Contrasena:</label>
	<input type="text" name="pass3" id="pass3" value="" tabindex="6" /><br />
	<input type="submit" name="signup" id="signup" value="Sign_Up" tabindex="7" />
</form>
</body>
</html>

<?php } ?>
 