<?php
/**
 * Pagina Inicial del Proyecto
 */
require_once 'lib/class/NotAProfile.php';
if(NotAProfile::estaLogeado()){
	header("Location: ./home.php");
}else{

// Declaramos las varfiables que vamos a usar en el formulario para prevenir XSS por URL
$email = "";
$passw = "";
$pass2 = "";

if(isset($_POST['submit'])){
	// Se ha enviado el formulario para iniciar sesion
	$email = isset($_POST['email'])?$_POST['email']:"";
	$passw = isset($_POST['pass'])?$_POST['pass']:"";
	$error = NotAProfile::login($email, $passw);
	if($error==0){
		header("Location: ./notprofile.php");	
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
							$mensaje = NotAProfile::RegistrarUsuario($email, $pass);
							if(true){
								//header('Location:'.$app['url'].'test');
								echo($mensaje);
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
switch($error){
	case 1:
		echo "<h2>Email or Password can not be empty </h2>";
		break;
	case 2:
		echo "<h2>Incorrect email format </h2>";
		break;
	case 3:
		echo "<h2>Wrong Email or/and password </h2>";
		break;
}
echo "<h1>".$msgError."</h1>";
?>
<div id=login>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<label for="email">E-mail:</label>
		<input type="text" name="email" id="email" value="<?php echo isset($_POST['email'])?$_POST['email']:"";?>" tabindex="1" /><br />
		<label for="pass">Password:</label>
		<input type="password" name="pass" id="pass" value="" tabindex="2" /><br />
		<input type="submit" name="submit" id="submit" value="Submit" tabindex="3" />
	</form>
</div>
<hr></hr>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="email2">E-mail:</label>
	<input type="text" name="email2" id="email2" value="<?php echo isset($_POST['email2'])?$_POST['email2']:""; ?>" tabindex="4" /><br />
	<label for="pass2">Contrasena:</label>
	<input type="password" name="pass2" id="pass2" value="" tabindex="5" /><br />
	<label for="pass3">Confirmar Contrasena:</label>
	<input type="password" name="pass3" id="pass3" value="" tabindex="6" /><br />
	<input type="submit" name="signup" id="signup" value="Sign_Up" tabindex="7" />
</form>
</body>
</html>

<?php } ?>
 