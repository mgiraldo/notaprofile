<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');
include('cerrarSiThickbox.php');
//Cuando filtro es 1 se muestran las claimed, sino, las orphans.
$filtro = isset($_GET['filtro'])?$_GET['filtro']:0;
$llaves = $filtro==0 ? NotAProfile::darLlavesDisponibles() : NotAProfile::darLlavesCreadasReclamadas();
$count = count($llaves);
if(!NotAProfile::estaLogeado())
{
	header("Location: /");
}
if(isset($_GET['del']))
{
	NotAProfile::eliminarLlave($_GET['del']);
	header("Location: /view/orphans");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>not_a_profile: keyring</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
	
	<?php foreach ($llaves as &$llave) {echo "var llave".$llave['id'].";";} ?>

	function initialize() {
		var latlng = new google.maps.LatLng(4.620913,-74.083643);
		var myOptions = {zoom: 13, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP};
		var map = new google.maps.Map(document.getElementById("gmap"), myOptions);
		var visibleInfoWindow = null;

		// Definimos los iconos de las llaves
		var iconoNoReclamado = '/img/llave.png';

		//Agregamos las llaves
		
		<?php foreach ($llaves as &$llave) { ?>
		    var posicionLlave<?php echo $llave['id'] ?> = new google.maps.LatLng(<?php echo $llave['latitud'] ?>, <?php echo $llave['longitud'] ?>);
		    var contenidoLlave<?php echo $llave['id'] ?> = '<b> Location:</b> <?php echo $llave['latitud'] ?>,<?php echo $llave['longitud'] ?> <br /><b>Created:</b><?php echo date("M j/Y",strtotime($llave['fecha_creado'])) ?><br />';
			<?php if (NotAProfile::puedeSerVista($llave)) { ?>
			contenidoLlave<?php echo $llave['id'] ?> += '<a href="/key/<?php echo $llave['codigo'] ?>">view</a>';
			<?php } ?>

			<?php if (NotAProfile::puedeSerVista($llave)&& ($filtro==0)) { ?>
			contenidoLlave<?php echo $llave['id'] ?> += ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><a href="/visualizacionLlaves.php?filtro=0&del=<?php echo $llave['codigo'] ?>">delete</a>';
			<?php } ?>
	
		    var infoWindow<?php echo $llave['id'] ?> = new google.maps.InfoWindow({content: contenidoLlave<?php echo $llave['id']?>, maxWidth: 350 });
		    llave<?php echo $llave['id'] ?> = new google.maps.Marker({position: posicionLlave<?php echo $llave['id'] ?>, map: map, icon: iconoNoReclamado });
		    google.maps.event.addListener(llave<?php echo $llave['id'] ?>, 'click', function() {if(visibleInfoWindow){ visibleInfoWindow.close(); } infoWindow<?php echo $llave['id'] ?>.open(map,llave<?php echo $llave['id'] ?>);  visibleInfoWindow = infoWindow<?php echo $llave['id'] ?>; map.setZoom(13);});
	   <?php }?> 
	}
	function mostrar(id){  google.maps.event.trigger(id, 'click');  }
	  
</script>
</head>
<body onload="initialize()">
<div id="contenido">
	<?php include("./inc/cabezote.php"); ?>
		
		<div id="cuerpo">
			<div id="llaves">
				<!--
				cambiar el class segun el tab activo:
				orphans: orphansactiva
				claimed: claimedactiva
				class = dinámico
				-->
				<?php if($filtro==1){$class ="claimedactiva";}else{$class ="orphansactiva";}?>
				<div id="mapatabs" class="<?php echo($class); ?>">
					<div class="orphans"><a href="/view/orphans">orphans</a></div>
					<div class="claimed"><a href="/view/claimed">claimed</a></div>					
				</div>
				<ul id="listallaves">
					<?php foreach ($llaves as &$llave) {?>
						<li>
						<div class="fotollave">
							<!-- si la llave es mia o yo la reclame me sale la foto -->
							<!-- si no es mia o no la reclame me sale generica -->
							<?php if(NotAProfile::puedeSerVista($llave)){?>
								<img src=<?php if(isset($llave['foto'])&&$llave['foto']!=""&&$llave['foto']!="error_nofile"){echo("/photos/".$llave['foto']."_t.jpg");}else{echo("/img/fotogenerica.gif");}?> width="61" height="61" />
							<?php }else{?>
								<img src="/img/fotogenerica.gif" width="61" height="61" />
							<?php } // Termina if puedeSerVista ?>
						</div>
						<div class="textollave">
							<!-- esto sale si la llave es MIA o YO LA RECLAME -->
							<?php if(NotAProfile::puedeSerVista($llave))
							{
								$txt = substr($llave['txt'], 0, 20);
								$txt .= strlen($llave['txt'])>20 ? "...": "";
							?>
							<span class="texto"><?php //echo($txt)?></span><br />
							<span class="loc"><?php echo($llave['latitud'].",".$llave['longitud'])?> </span><br />
							<?php }?>
							<a href="javascript:void(0);" onclick= "mostrar(llave<?php echo $llave['id']; ?>);"> view </a>
						</div>
					</li>
					<?php } // Termina foreach?>
				</ul>
			</div>
			
		<div id="mapa">
			<div id="navmapa">
				<a class="createkey" href="/create"><span>create_a_key</span></a>
			</div>
			<div id="gmap">
				
			</div>
		</div>
	
	
		</div>
		<?php include("./inc/pie.php"); ?>
	</div>
</div>
</body>
</html>


