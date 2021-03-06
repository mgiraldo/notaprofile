<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');

$usuariorelacion= $_GET['usr'];
if(!NotAProfile::estaLogeado()||!isset($usuariorelacion)||$usuariorelacion=="")
{
	header("Location: /");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
<title>not_a_profile</title>
</head>

<body>
<div id="contenido">

	<?php include("./inc/cabezote.php"); ?>
    
	<div id="cuerpo">
    
	<div id="estadisticas">
		<div id="espacio_up">
		&nbsp;
		</div>
		<div id="contenedor">
			<div id="textoestadistica" class="bold">
			Compatibility:
			</div>
			<div id="num_estadistica" class="bold">
			<?php 
			$compatibilidad=NotAProfile::compatibilidad($usuariorelacion);
			echo($compatibilidad);
			
			//INTENTO DE BARRITA
			//if($compatibilidad>0){?>
			<!--  <div class="divbarra"><p class="barrapositiva" style="width: <?php //echo($compatibilidad)*5;?>%;"><span><?php //echo($compatibilidad);?>%</span></p></div> -->
			<?php // }else if($compatibilidad<0){?>
			<!-- <div class="divbarra"><p class="barranegativa" style="width: <?php //echo($compatibilidad)*-5;?>%;"><span><?php //echo($compatibilidad);?>%</span></p></div> -->
			<?php // }?>
			</div>
		</div>
		<div id="contenedor">
			<div id="textoestadistica" class="me">
			You like:
			</div>
			<div id="num_estadistica" class="me">
			<?php  echo(NotAProfile::youLike($usuariorelacion)); ?>
			</div>
		</div>
		<div id="contenedor">
			<div id="textoestadistica" class="me">
			You dislike:
			</div>
			<div id="num_estadistica" class="me">
			<?php  echo(NotAProfile::youDislike($usuariorelacion)); ?>
			</div>
		</div>
		<div id="contenedor">
			<div id="textoestadistica" class="they">
			he/she likes:
			</div>
			<div id="num_estadistica" class="they">
			<?php  echo(NotAProfile::heLikes($usuariorelacion)); ?>
			</div>
		</div>
		<div id="contenedor">
			<div id="textoestadistica" class="they">
			he/she dislikes:
			</div>
			<div id="num_estadistica"  class="they">
			<?php  echo(NotAProfile::heDislikes($usuariorelacion)); ?>
			</div>
		</div>
	</div>
    <div id="llavescompartidas">
				<a class="createkey" href="/create"><span>create_a_key</span></a>
   </div>
    	<div id="contenedor_lista">	
    		<?php $llaves =  NotAProfile::darLlavesRelacion($usuariorelacion);?>
			<ul id="listaconexionindividual">
				<?php foreach ($llaves as $llave){?>
				<li <?php if($llave['creador_id']==$_SESSION['userid']){echo("class=\"yo\"");}else{echo("class=\"ellos\"");}?>>
					<a href="/key/<?php echo($llave['codigo'])?>" >
					<?php $foto = $llave['foto'];?>
					<?php if($foto!=NULL && $foto!="" && $llave['foto']!="error_nofile") {?>
					<img src="<?php echo("/".$app['photoroot'].$foto."_m.jpg")?>" alt="123" width="128" height="128" /></a>
					<?php }else{?>
					<img src="/img/btn_dislike.png" alt="123" width="128" height="128" />
					<?php }?>
					</a>
				</li>
				<?php }?>
			</ul>
		</div>
	  </div>
	  <?php include("./inc/pie.php"); ?>
</div>
</body>
</html>