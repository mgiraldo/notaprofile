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
	header("Location: /m?r=" . urlencode("/m/view"));
}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>not_a_profile : keys nearby</title>
<link href="/css/estilosMob.css" rel="stylesheet" type="text/css" />
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport"/>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var map;
var me;
var watchId;
function invokeLocation() {
	navigator.geolocation.getCurrentPosition(gMapper);
}
function gMapper (position) {
	var myLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
	map.setCenter(myLatLng, 18);
	var meIcon = "/img/me.gif";
    me = new google.maps.Marker({position: myLatLng, map: map, icon: meIcon, draggable: false, clickable: false, bouncy: false});
	watchId = navigator.geolocation.watchPosition(theWatcher);
}
function theWatcher(position) {
	var myLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
    me.setPoint(myLatLng);
}

	<?php foreach ($llaves as &$llave) {echo "var llave".$llave['id'].";";} ?>

	function initialize() {
		var latlng = new google.maps.LatLng(4.620913,-74.083643);
		var myOptions = {zoom: 13, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP};
		map = new google.maps.Map(document.getElementById("gmap"), myOptions);
		var visibleInfoWindow = null;
		invokeLocation();

		// Definimos los iconos de las llaves
		var iconoNoReclamado = '/img/llave.png';

		//Agregamos las llaves
		
		<?php foreach ($llaves as &$llave) { ?>
		    var posicionLlave<?php echo $llave['id'] ?> = new google.maps.LatLng(<?php echo $llave['latitud'] ?>, <?php echo $llave['longitud'] ?>);
		    var contenidoLlave<?php echo $llave['id'] ?> = '<b> Location:</b> <?php echo $llave['latitud'] ?>,<?php echo $llave['longitud'] ?> <br /><b>Created:</b><?php echo date("M j/Y",strtotime($llave['fecha_creado'])) ?><br />';
			<?php if (NotAProfile::puedeSerVista($llave)) { ?>
			contenidoLlave<?php echo $llave['id'] ?> += '<a href="/key/<?php echo $llave['codigo'] ?>">view</a>';
			<?php } ?>

		    var infoWindow<?php echo $llave['id'] ?> = new google.maps.InfoWindow({content: contenidoLlave<?php echo $llave['id']?>, maxWidth: 350 });
		    llave<?php echo $llave['id'] ?> = new google.maps.Marker({position: posicionLlave<?php echo $llave['id'] ?>, map: map, icon: iconoNoReclamado });
		    google.maps.event.addListener(llave<?php echo $llave['id'] ?>, 'click', function() {if(visibleInfoWindow){ visibleInfoWindow.close(); } infoWindow<?php echo $llave['id'] ?>.open(map,llave<?php echo $llave['id'] ?>);  visibleInfoWindow = infoWindow<?php echo $llave['id'] ?>; map.setZoom(13);});
	   <?php }?> 
	}
	function mostrar(id){  google.maps.event.trigger(id, 'click');  }
	  
</script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<body onload="initialize()" onunload="GUnload()">
<?php include("./inc/cabezoteMob.php"); ?>
<div id="gmap">Location unknown</div>
</body>
</html>