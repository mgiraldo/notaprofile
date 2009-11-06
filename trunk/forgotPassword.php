<?php 
require_once 'lib/class/NotAProfile.php';
$msg = "Type your email. A code will be sended with a reactivation code.";
$cambiarClave = "error";
if(isset($_POST['send']))
{
	$exito = NotAProfile::enviarEmailCambioClave($_POST['email']);
	if(!$exito)
	{
		$msg = "You typed a non valid email.";
	}
	else
	{
		$msg = "an email has just been sended with a reactivation code.";
	}
}
else if(isset($_GET['c'])&&isset($_GET['e']))
{
	$msg = "Type and retype your new password.";	
	$cambiarClave = NotAProfile::existeCambioClave($_GET['e'],$_GET['c']);	
}
else if(isset($_POST['email'])&&isset($_POST['pass2'])&&isset($_POST['pass3'])&&isset($_POST['act']))
{
	$msg = "your password has been changed.";	
	$error = NotAProfile::cambiarClave($_POST['email'],$_POST['pass2'],$_POST['pass3'],$_POST['act']);	
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
		echo "<h2>Email does not match activation code</h2>";
		break;
	case 5:
		echo "<h2>Error in Database procesing!! Contact webmaster righ now!!!!</h2>";
		break;
		
	case 0:
		echo "<h2> Your password has been changed, you can now sign in again</h2>";
		break;
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
<?php echo($msg);
if($cambiarClave!="error")
{ 
?>
<div id=change>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="pass2">Password:</label>
	<input type="password" name="pass2" id="pass2" value="" tabindex="5" /><br />
	<label for="pass3">Retype Password:</label>
	<input type="password" name="pass3" id="pass3" value="" tabindex="6" /><br />
	<input type="hidden" value="<?php echo($cambiarClave);?>" name="email" tabindex="6" /><br />
	<input type="hidden" value="<?php echo($_GET['c']);?>" name="act" tabindex="6" /><br />
	</form>
</div>
<?php 
}else{?>
<div id=forgot>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<label for="email">E-mail:</label>
		<input type="text" name="email" id="email" value="<?php echo isset($_POST['email'])?$_POST['email']:"";?>" tabindex="1" /><br/>
		<input type="submit" name="send" id="submit" value="Submit" tabindex="3" />
	</form>
</div>
<?php }?>
</body>
</html>