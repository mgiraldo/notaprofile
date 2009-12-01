<?php
/**
 * Pagina Inicial del Proyecto
 */
require_once 'lib/class/NotAProfile.php';

if(isset($_GET['logout'])){
	NotAProfile::cerrarSesion();
	header("Location: ./");
}



if(NotAProfile::estaLogeado()){
	//header("Location: ./notprofile.php");
	header("Location: /view");
}else{
	


// Declaramos las variables que vamos a usar en el formulario para prevenir XSS por URL
$email = "";
$passw = "";
$pass2 = "";

if(isset($_POST['typesubmit']) && $_POST['typesubmit']==1){
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
				if (!isset($_POST['r'])) {
					//header("Location: ./notprofile.php?tb=true");
					header("Location: /view");
				} else {
					header("Location: " . $_POST['r']);
				}
		}
	
}else if(isset($_POST['typesubmit']) && $_POST['typesubmit']==2){
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
				if (!isset($_POST['r'])) {
					//header("Location: ./notprofile.php?tb=true");
					header("Location: /view");
				} else {
					header("Location: " . $_POST['r']);
				}
		}
	}	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
<link href="/css/estilosLogin.css" rel="stylesheet" type="text/css" />
<title>not_a_profile</title>
</head>
<script languaje="javascript">
function escondidoInicial(caso){
	if(caso==1)	{
		document.getElementById("englobador_sign_up").style.visibility = 'hidden';
		document.getElementById("botonSignUp").disabled = false;
		document.getElementById("botonSignIn").style.visibility = 'visible';
		document.getElementById("botonSignIn").disabled = false;
		document.getElementById("typesubmit").value = 1;
	}else{
		document.getElementById("checkbox").checked=true;
		document.getElementById("botonSignIn").style.visibility = 'hidden';
		document.getElementById("botonSignIn").disabled = true;
    	document.getElementById("englobador_sign_up").style.visibility = 'visible';
		document.getElementById("botonSignUp").disabled = false;
		document.getElementById("typesubmit").value = 2;
	}
}

function habilitaDeshabilita(form){
    if (form.checkbox.checked == true)    {
		document.getElementById("botonSignIn").style.visibility = 'hidden';
		document.getElementById("botonSignIn").disabled = true;		
    	document.getElementById("englobador_sign_up").style.visibility = 'visible';
		document.getElementById("botonSignUp").disabled = false;
		document.getElementById("typesubmit").value = 2;
    } else{
		document.getElementById("botonSignIn").style.visibility = 'visible';
		document.getElementById("botonSignIn").disabled = false;
    	document.getElementById("englobador_sign_up").style.visibility = 'hidden';
		document.getElementById("botonSignUp").disabled = true;
		document.getElementById("typesubmit").value = 1;
    }
}
</script>

<body onload="escondidoInicial(<?php if(isset($error)){if($error==3) echo($error); else echo (1);}else{echo(1);}?>);">
<div id="contenido">
	<div id="cabezote">
		<h1>not_a_profile</h1>
	</div>
    <form id="cuerpo_sign" method="post" name="loginsignupform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="typesubmit" id="typesubmit" value="1" />
		<div id="englobador_sign_in">
<?php
if(isset($_POST['typesubmit']) && $_POST['typesubmit']==1){
switch($error){
	case 1:
		echo "<div id=\"errorLogin\">email or password cannot be empty</div>";
		break;
	case 2:
		echo "<div id=\"errorLogin\">incorrect email format</div>";
		break;
	case 3:
		echo "<div id=\"errorLogin\">wrong email or password</div>";
		break;
}}
if(isset($_POST['typesubmit']) && $_POST['typesubmit']==2){
switch($error){
	case 1:
		echo "<div id=\"errorLogin\">email or password cannot be empty </div>";
		break;
	case 2:
		echo "<div id=\"errorLogin\">incorrect email format</div>";
		break;
	case 3:
		echo "<div id=\"errorLogin\">the passwords do not match!</div>";
		break;
	case 4:
		echo "<div id=\"errorLogin\">email already registered (use another)</div>";
		break;
	case 5:
		echo "<div id=\"errorLogin\">error in database processing :(</div>";
		break;
		
	case 0:
		echo "<div id=\"errorLogin\"> SE HA REGISTRADO!! YA PUEDE INICIAR SESION!!</div>";
		break;
			
}}
?>

			<div id="header"><h2>sign_in</h2> </div>
            <div id="boton">&nbsp;</div>
            <div id="text_box"><input id="box" type="text" name="email" value="<?php echo isset($_POST['email'])?$_POST['email']:""; ?>" tabindex="1"></div>
            <div id="sign"><h3>your email:</h3></div>            
            <div id="boton"><input type="image" id="botonSignIn" src="/img/enter.png" value="Sign_In" height="26" width="195" border="0" vspace="0"  alt="Sing in" tabindex="3" /></div>
            <div id="text_box"><input id="box" type="password" name="pass" tabindex="2" /></div>
            <div id="sign"><h3>password:</h3></div>
			<div id="boton">&nbsp;</div>
			<div id="sign_link"><h4><a href="/forgotPassword.php">forgot_password</a></h4></div>
			<div id="boton">&nbsp;</div>
			<div id="boton">&nbsp;</div>
			<div id="checkbox_1"><input type="checkbox" id="checkbox" name="checkbox" onclick="habilitaDeshabilita(this.form);" value="checkbox" tabindex="4">
			<label for="checkbox">First time here?</label></div>
		</div>
        <div id="englobador_sign_up">
            <div id="header"><h2>sign_up</h2></div>
            <div id="info">just_rewrite_your_password:</div>
            <div id="boton"><input type="image" id="botonSignUp" src="/img/submit.png" value ="Sign_Up" height="26" width="195" border="0" vspace="0" alt="Sign up"  tabindex="6" />  
            </div><div id="text_box"><input id="box" type="password" name="pass2" tabindex="5" /></div>
            <div id="sign"><h3>password:</h3></div>
        </div>
        <?php if(isset($_GET['tb'])) { ?>
			<input type="hidden" value="<?php echo($_GET['reclamollave'])?>" name="reclamollave" id="reclamollave" />
		<?php } ?>
        <?php if(isset($_GET['r'])) { ?>
			<input type="hidden" value="<?php echo($_GET['r'])?>" name="r" id="r" />
		<?php } ?>
    </form>
	<div id="catch">
	<?php if(isset($_GET['r'])){ ?>
		<h2>there is a key for you!</h2>
		<p>but you need to log in...</p>
		<p>not_a_profile is about real connections with real people. we would like to avoid using passwords and emails but we need a way to distinguish you from the average Joe. Don't worry, we won't send you any spam (we hate it as much as you) and we will encrypt your password.</p>
	<?php }
	else {?>
		<h2>welcome!</h2>
		<p>not_a_profile is about real connections with real people. we would like to avoid using passwords and emails but we need a way to distinguish you from the average Joe. Don't worry, we won't send you any spam (we hate it as much as you) and we will encrypt your password.</p>
	<?php }?>
	</div>
    <?php include("./inc/pie.php"); ?>

</div>
</body>
</html>

<?php } ?>