<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');

if(isset($_POST["latitud"])&&isset($_POST["longitud"])&&isset($_POST["texto"]))
{
	$codigo = NotAProfile::crearLlave($_POST["latitud"],$_POST["longitud"],$_POST["texto"]);
	if($codigo!="error")
	{
	 echo("Llave creada exitosamente, el c&oacute;digo es: ". $codigo);
	}
	else
	{
	  echo("Error al crear Llave");
	}
}

?>
<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>not_a_profile</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA18qEiHphe4mikuwMZVbDfBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxR3ccS3aeoyDwYsLjU2Gj3FcJ7nNw&sensor=true_or_false"
            type="text/javascript"></script>
    <script type="text/javascript">
    var map;
    var llave;
    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng(4.620913,-74.083643), 13);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        GEvent.addListener(map, "click", function(overlay,latlng) {
				llave = latlng;
				document.getElementById("lat").value = latlng.lat();
				document.getElementById("lng").value = latlng.lng();
				map.clearOverlays();
      	  		map.addOverlay(crearMarker(llave));
        	});
      }
    }

    function crearMarker(latlng)
    {
         var baseIcon = new GIcon(G_DEFAULT_ICON);
         baseIcon.image = "llave.png";
         var tamanio = 16;
         baseIcon.iconSize = new GSize(tamanio, tamanio+tamanio*0.7);
         baseIcon.shadowSize = new GSize(37, 34);
         baseIcon.iconAnchor = new GPoint(9, 34);
         baseIcon.infoWindowAnchor = new GPoint(9, 2);

         markerOptions = { icon:baseIcon };
         var marker = new GMarker(latlng,markerOptions);
         return marker;
    }

    </script>
  </head>
  <body onload="initialize()" onunload="GUnload()">
    <div id="map_canvas" style="width: 500px; height: 300px"></div>
    <form method="post">
    Latitud: <input type = "text" name="latitud" id="lat"></input>
    <br>
    Longitud: <input type = "text" name="longitud" id="lng"></input>
    <br>
    Texto: <input name="texto" type = "text"></input>
    <br>
    <input type="submit" value="Crear Llave"></input>
    </form>
    <a href="visualizacionLlaves.php">ver todas las llaves disponibles</a>
  </body>
</html>