<?php
require_once 'lib/class/NotAProfile.php';

		$idllave = $_GET['c'];
		$llave = NotAProfile::darLlave($_GET['c']);
		$texto = $llave[0]['txt']; 
		$primerosCaracteres = $primerosCaracteres = substr($texto, 0, 100) . '...';
		
		if(isset($_GET['like']))
		{	$gusta = $_GET['like'];
			if($gusta == 1){
				NotAProfile::aceptarLlave($idllave);
				header( 'Location: ' . $_SERVER['PHP_SELF']."?c=$idllave");
			}
			else{
				NotAProfile::rechazarLlave($idllave);
				header( 'Location: ' . $_SERVER['PHP_SELF']."?c=$idllave");
			}
		}
		
		if(NotAProfile::estaLogeado())
		{
			$creador_id= $_SESSION['userid'];
			$recibo = NotAProfile::reclamarLlave($_GET['c'],$creador_id);	
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/estilos.css" rel="stylesheet" type="text/css" />
<title>not_a_profile: <?php echo $primerosCaracteres ?></title>
<style>
#fotollave {
	background: url(<?php echo ($app['photoroot'].$llave[0]['foto'].".jpg") ?>) repeat-x;
}
</style>

	<?php if(!NotAProfile::estaLogeado()){ ?>
			
<script type="text/javascript" src="lib/javascript/jquery.js"></script>
<script type="text/javascript" src="lib/javascript/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<script> 
	$(document).ready(function(){ 
	tb_show("","index.php?tb=1&reclamollave=<?php echo($_GET['c']) ?>&KeepThis=true&TB_iframe=true&height=400&width=600&modal=true",""); 
	}); 
</script>
		
	<?php }?>

</head>

<body>
<div id="fotollave">
	<div id="gradiente"> </div>
</div>
<div id="contenido">

	<?php
	if($llave[0]['flag_aceptado'] == "0"){ 
		include("./inc/cabezotellave.php");
	}
	else{
		include("./inc/cabezote.php");
	}	
	?>	
	
	<div id="cuerpo">
		<?php if($llave[0]['flag_aceptado'] == "0") {?>
		<div id="dislike"><a href="?like=-1&c=<?php echo $llave[0]['codigo']?>"><span>dislike</span></a></div>
		<?php }?>
		
		<div id="textollave">
		<?php echo($llave[0]['txt']); ?>
		</div>
		
		<?php if($llave[0]['flag_aceptado'] == "0") {?>
		<div id="like"><a href="?like=1&c=<?php echo $llave[0]['codigo']?>"><span>like</span></a></div>
		<?php }?>
		
	</div>
	<?php include("./inc/pie.php");?>
</div>
</body>
</html>