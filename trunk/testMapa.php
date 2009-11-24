<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');

$llaves = NotAProfile::darLlavesDisponibles();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>not_a_profile</title>
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="http://gmaps-utility-library.googlecode.com/svn/trunk/markermanager/release/src/markermanager.js"></script>

<script type="text/javascript">

	<?php
	foreach ($llaves as &$llave) {
  		echo "var".$llave['id']."; ";
  	}
  
  ?>
  function initialize() {
	  
    var latlng = new google.maps.LatLng(4.620913,-74.083643);
    var myOptions = {zoom: 13, center: latlng, mapTypeId: google.maps.MapTypeId.ROADMAP};
    var map = new google.maps.Map(document.getElementById("mapa"), myOptions);
    var visibleInfoWindow = null;

    // Definimos los iconos de las llaves
    var iconoNoReclamado = 'llave.png';

    //Agregamos las llaves
    
    
     <?php 
    foreach ($llaves as &$llave) {
    echo "	var posicionLlave".$llave['id']." = new google.maps.LatLng(".$llave['latitud'].", ".$llave['longitud']."); ";
    $txt = substr($llave['txt'], 0, 50);$txt .= strlen($llave['txt'])>50 ? "...": "";
    echo "	var contenidoLlave".$llave['id']." = '<b> Informacion de la llave: </b> <br /> <p>Esta llave tiene el ID: ".$llave['id']."  *** </p><br/><b>Texto:<b><p></p>".$txt."'; ";
    echo "	var infoWindow".$llave['id']." = new google.maps.InfoWindow({content: contenidoLlave".$llave['id'].", maxWidth: 350 }); ";
    echo "	llave".$llave['id']." = new google.maps.Marker({position: posicionLlave".$llave['id'].", map: map, icon: iconoNoReclamado }); ";
    echo "	google.maps.event.addListener(llave".$llave['id'].", 'click', function() {if(visibleInfoWindow){ visibleInfoWindow.close(); } infoWindow".$llave['id'].".open(map,llave".$llave['id'].");  visibleInfoWindow = infoWindow".$llave['id']."; });\n";
    }
    ?>    
  }

  function mostrar(id){  google.maps.event.trigger(id, 'click');  }
	  
</script>

</head>
<body onload="initialize()">
	<div id="mapa" style="width: 690px; height: 470px"></div>
	<a href="javascript:void(0);" onclick= "mostrar(llave1);"> ver llave 01 </a>
	<a href="javascript:void(0);" onclick= "mostrar(llave36);"> ver llave  36</a>  
</body>
</html>