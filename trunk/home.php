<?php

/**
 * Este archivo corresponde a la pagina donde se encuentran todas las opciones
 * que puede realizar el usuario registrado
 */

require_once 'lib/class/NotAProfile.php';
if(!($usuario->logged_in)){
	header("Location: ./index.php");
}
if(isset($_GET['logout'])){
	NotAProfile::cerrarSesion();
	header("Location: ./index.php");
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

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="submit" name="logout" id="logout" value="Logout" tabindex="7" />
</form>

</body>
</html>
