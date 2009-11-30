<?php 
require_once 'lib/class/NotAProfile.php';
if(!NotAProfile::estaLogeado())
{
	header("Location: index.php");
}
$contactos = NotAProfile::darContactos();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/estilos.css" rel="stylesheet" type="text/css" />
<title>not_a_profile</title>
</head>

<body>
<div id="contenido">
	<?php include("./inc/cabezote.php"); ?>
	<div id="cuerpo">
    
    <div id="navmapa">
				<a class="createkey" href="/create"><span>create_a_key</span></a>
   </div>
    		
		<ul id="listaconexiones">
		<?php for ($index = 0; $index < count($contactos); $index++) {
		if($contactos[$index]['reclamados_el']==NULL)
		{
			$ella=0;
		}
		else
		{
			$ella = $contactos[$index]['reclamados_el'];
		}	
		
		if($contactos[$index]['reclamados_yo']==NULL)
		{
			$yo = 0;
		}
		else
		{
			$yo = $contactos[$index]['reclamados_yo'];
		}
		?>
		
		<?php 
		//se mira si es el último de la lista
		if($index%7==0 && $index>0)
		{
		?>
        <li class="ultimo">
        <?php 
		}else{
        ?>
        <li>
        <?php }?>
        	<?php 
        	if($contactos[$index]['amigo_idc1']!=NULL)
        	{
        		$foto = NotAProfile::darThumbnailRelacion($contactos[$index]['amigo_idc1']);
        	}
        	else
        	{
        		$foto = NotAProfile::darThumbnailRelacion($contactos[$index]['amigo_idc2']);
        	}
        	?>
        	<?php if($foto!=NULL && $foto!="" && $llave['foto']!="error_nofile"){?>
		  	<a href="#" class="imagen"><img src="<?php echo($foto)?>" alt="/img/btn_like.png" width="128" height="128" /></a>
			<?php 
        	}else{
			?>
			<a href="#" class="imagen"><img src="img/btn_like.png" alt="123" width="128" height="128" /></a>
			<?php }?>
		  	<div><span class="yo"><?php echo($yo)?></span> / <span class="ella"><?php echo($ella)?></span></div>
		</li>
		<?php }?>
			</ul>
	  </div>

	
<div id="pie">
		<a href="#">help</a> | <a href="#">about this project</a> | <a href="#">google code</a> | <a href="#">copyright notice</a>
	</div>

</div>
</body>
</html>
