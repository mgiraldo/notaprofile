<?php
/**
 * Pagina Inicial del Proyecto
 */
require_once 'lib/class/NotAProfile.php';
if(NotAProfile::estaLogeado()){
	header("Location: ./notprofile.php");
}else{

// Declaramos las variables que vamos a usar en el formulario para prevenir XSS por URL
$email = "";
$passw = "";
$pass2 = "";

if(isset($_POST['submit'])){
	// Se ha enviado el formulario para iniciar sesion
	$email = isset($_POST['email'])?$_POST['email']:"";
	$passw = isset($_POST['pass'])?$_POST['pass']:"";
	$error = NotAProfile::login($email, $passw);
	if(isset($_POST['reclamollave'])&&$error==0)
	{
		$idreclamador= $_SESSION['userid'];
		$ll = NotAProfile::rereclamarLlave($_POST['reclamollave'],$idreclamador);
	}
	if($error==0){
				header("Location: ./notprofile.php?tb=true");
		}
	
}else if(isset($_POST['signup'])){
	// Se ha enviado el formulario para registrarse

	$email = isset($_POST['email'])?$_POST['email']:"";
	$passw = isset($_POST['pass'])?$_POST['pass']:"";
	$passw2 = isset($_POST['pass2'])?$_POST['pass2']:"";
	$error = NotAProfile::registrarUsuario($email, $passw, $passw2);
	if($error==0){
	$error = NotAProfile::login($email, $passw);
		if(isset($_POST['reclamollave'])&&$error==0)
		{
			$idreclamador= $_SESSION['userid'];
			$ll = NotAProfile::rereclamarLlave($_POST['reclamollave'],$idreclamador);
		}
		if($error==0){
				header("Location: ./notprofile.php?tb=true");
		}
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
<body onload="escondidoInicial(<?php if(isset($error)){echo($error);}else{echo(1);}?>);">
<h1>not_a_profile</h1>
<?php

if(isset($_POST['submit'])){
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
}}
if(isset($_POST['signup'])){
switch($error){
	case 1:
		echo "<h2>Email or Password can not be empty </h2>";
		break;
	case 2:
		echo "<h2>Incorrect email format </h2>";
		break;
	case 3:
		echo "<h2>The passwords do not match!</h2>";
		break;
	case 4:
		echo "<h2>Email already in use</h2>";
		break;
	case 5:
		echo "<h2>Error in Database procesing!! Contact webmaster righ now!!!!</h2>";
		break;
		
	case 0:
		echo "<h2> SE HA REGISTRADO!! YA PUEDE INICIAR SESION!!</h2>";
		break;
			
}}
?>
<script languaje="javascript">
function escondidoInicial(caso)
{
	if(caso==1)
	{
		document.getElementById("logindiv").style.visibility = '';
		document.getElementById("signupdiv").style.visibility = 'hidden';
	}
	else
	{
		document.getElementById("nuevo").checked=true;
    	document.getElementById("signupdiv").style.visibility = '';
    	document.getElementById("logindiv").style.visibility = 'hidden';
	}
}
function habilitaDeshabilita(form)
{
    if (form.nuevo.checked == true)
    {
    	document.getElementById("signupdiv").style.visibility = '';
    	document.getElementById("logindiv").style.visibility = 'hidden';
    }
    else
    {
    	document.getElementById("logindiv").style.visibility = '';
    	document.getElementById("signupdiv").style.visibility = 'hidden';
    }
}
</script>
<div>
	<form method="post" name="loginsignupform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<label for="email">E-mail:</label>
			<input type="text" name="email" id="email" value="<?php echo isset($_POST['email'])?$_POST['email']:""; ?>" tabindex="4" /><br />
			<label for="pass">Pass:</label>
			<input type="password" name="pass" id="pass" value="" tabindex="5" /><br />
			<input type="checkbox" id="nuevo" name="nuevo" onclick="habilitaDeshabilita(this.form);" value="nuevo">I'm new<br />
			<div id="signupdiv">
				<label for="pass2">Pass2:</label>
				<input type="password" name="pass2" id="pass2" value="" tabindex="6" /><br />
				<input type="submit" name="signup" id="signup" value="Sign_Up" tabindex="7" />
			</div>
			<div id="logindiv">
				<input type="submit" name="submit" id="login" value="Log_in" tabindex="7" />
				<a href="forgotPassword.php">forgot_my_password</a>
			</div>
			<?php 
			if(isset($_GET['tb']))
			{
			?>
			<input type="hidden" value="<?php echo($_GET['reclamollave'])?>" name="reclamollave" id="reclamollave"></input>
			<?php 
			}
			?>
	</form>
</div>
</body>
</html>
<?php } ?>