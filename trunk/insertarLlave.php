<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');
//echo("id=".$_SESSION['userid']);

if(isset($_POST["latitud"])&&isset($_POST["longitud"])&&isset($_POST["texto"]))
{
	//echo(NotAProfile::subirFoto('image'));
	$codigo = NotAProfile::crearLlave($_POST["latitud"],$_POST["longitud"],$_POST["texto"]);
	if($codigo!="error")
	{
	 echo("key_created: ". $codigo);
	}
	else
	{
	  echo("error_creating_key");
	}
}

?>
<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>not_a_profile</title>
    <script src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $app['apiKey'] ?>&sensor=true"
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
    <form method="post" enctype="multipart/form-data">
    <table>
    <tr>
    <td>
    latitude: 
    </td>
    <td>
    <input type = "text" name="latitud" readonly="readonly" id="lat"></input>
    </td>
	</tr>
	<tr>
	<td>
    longitude: 
    </td>
    <td>
    <input type = "text" name="longitud" readonly="readonly" id="lng"></input>
    </td>
    </tr>
    <tr>
    <td>
    txt: 
    </td>
    <td>
    <textarea name="texto" rows="2" cols="20" ></textarea>
    </td>
    </tr>
    <tr><td><input type="file" name="image"></td></tr>
    <tr>
    <td>
    <input type="submit" value="make_key"></input>
    </td>
    <td>
    </td>
    </tr>
    </table>
	 	
    </form>
    <a href="visualizacionLlaves.php">check_out_keys</a>
    <br>
    <a href="notprofile.php">my_not_profile</a>
  </body>
</html>