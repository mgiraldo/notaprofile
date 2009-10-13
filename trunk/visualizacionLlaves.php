<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');

$llaves = NotAProfile::listaLlavesDisponibles();
$count = count($llaves);
?>
<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>not_a_profile</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo($app["apiKey"]); ?>&sensor=true_or_false"
            type="text/javascript"></script>
    <script type="text/javascript">
    var map;
    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng(4.620913,-74.083643), 13);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        map.enableScrollWheelZoom();
      }

          <?php
          	 for($i=0;$i<$count;$i++)
          	 {
          	 $fecha = new DateTime($llaves[$i]["fecha_creado"]);	
          	 $fechaFormato = $fecha->format('jS F Y');
          ?>
        map.addOverlay(crearMarker(<?php echo($llaves[$i]["latitud"]); ?>,<?php echo($llaves[$i]["longitud"]); ?>,"<?php echo($fechaFormato); ?>"));
        <?php 
        	}
         ?>
      
    }

    function crearMarker(lat,lon, fecha)
    {
         var point = new GLatLng(lat,lon);
         var baseIcon = new GIcon(G_DEFAULT_ICON);
         baseIcon.image = "llave.png";
         var tamanio = 20;
         baseIcon.iconSize = new GSize(tamanio, tamanio+tamanio*0.7);
         baseIcon.shadowSize = new GSize(37, 34);
         baseIcon.iconAnchor = new GPoint(9, 34);
         baseIcon.infoWindowAnchor = new GPoint(9, 2);

         markerOptions = { icon:baseIcon };
         var marker = new GMarker(point,markerOptions);
         GEvent.addListener(marker, "click", function() {
             marker.openInfoWindowHtml(" Llave dejada: " + fecha);
           });
         return marker;
    }

    </script>
  </head>
  <body onload="initialize()" onunload="GUnload()">
    <div id="map_canvas" style="width: 500px; height: 300px"></div>
    <a href="insertarLlave.php">crear una llave</a>
  </body>
</html>