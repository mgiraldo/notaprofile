<?php 
require_once('lib/class/classTextile.php');
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');
//echo("id=".$_SESSION['userid']);
if(!NotAProfile::estaLogeado())
{
	header("Location: /");
}
$textile = new Textile();
$msg = "";
if(isset($_POST["latitude"])&&isset($_POST["longitude"]))
{
	if(($_POST["texto"]!="")||($_FILES['image']["name"]!=""))
	{
		$foto=NULL;
		if($_FILES['image']["name"]!="")
		{
			$foto = NotAProfile::subirFoto('image');	
		}
		
		//Texto enriquecido
		$texto = $textile->TextileThis($_POST["texto"]);
		$codigo = NotAProfile::crearLlave($_POST["latitude"],$_POST["longitude"],$texto,$foto);
		
		
		if($codigo!="error")
		{
		 $msg = $codigo;
		 header( "Location: $codigo");
		}
		else
		{
		  $msg = "error_creating_key";
		}
		
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
<link href="/css/estilosInsertarLlave.css" rel="stylesheet" type="text/css" />
<title>not_a_profile</title>
</head>
 <script src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $app['apiKey'] ?>&sensor=true" type="text/javascript"></script>
    <script type="text/javascript">
var agent=navigator.userAgent.toLowerCase();
var is_iphone = ((agent.indexOf('iphone')!=-1));
if (is_iphone)
{ 	
	window.location = "/m/create" 
}
    var map;
    var llave;
    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("gmap"));
		var defaultLatLng = new GLatLng(4.620913,-74.083643);
        map.setCenter(defaultLatLng, 13);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        map.enableScrollWheelZoom();
        GEvent.addListener(map, "click", function(overlay,latlng) {
				llave = latlng;
				document.getElementById("latitude").value = latlng.lat();
				document.getElementById("longitude").value = latlng.lng();
				map.clearOverlays();
      	  		map.addOverlay(crearMarker(llave));
        	});
		llave = defaultLatLng;
		document.getElementById("latitude").value = defaultLatLng.lat();
		document.getElementById("longitude").value = defaultLatLng.lng();
		map.clearOverlays();
		var marker = crearMarker(llave);
		marker.openInfoWindowHtml("<div id=\"bubble\">your key will be created here or click your desired location</div>", {maxWidth:225});
      	map.addOverlay(marker);
      }
    }

    function crearMarker(latlng)
    {
         var baseIcon = new GIcon(G_DEFAULT_ICON);
         baseIcon.image = "/img/llave.png";
         var tamanio = 16;
         baseIcon.iconSize = new GSize(tamanio, tamanio+tamanio*0.7);
         baseIcon.shadowSize = new GSize(37, 34);
         baseIcon.iconAnchor = new GPoint(9, 34);
         baseIcon.infoWindowAnchor = new GPoint(9, 2);

         markerOptions = { icon:baseIcon };
         var marker = new GMarker(latlng,markerOptions);
         //var marker = new GMarker(latlng);
         return marker;
    }

    </script>
   <script>
    //  function preview(img,obj) {
    //     img.src = "file:///" + obj.value;
    //  }
   </script>

<body onload="initialize()" onunload="GUnload()">
<div id="contenido">

	<?php include("./inc/cabezote.php"); ?>
	<p><?php echo($msg);?></p>
	<div id="cuerpo_sign">
		<form method="post" enctype="multipart/form-data">
		<div id="create_key">
            <div id="preview_image">
                <img id="img" width='270' height='138' src="./img/nofoto.jpg" />
                <!--
                el div tiene el tama�o indicado, la pregunta concretamente es s� se muestra un preview de la imagen al sacarla del browser,
                por lo que tocar�a usar el convertidor de tama�os de imagen del que habla mauricio, o si simplemente no se muestra la imagen,
                tambi�n debe haber un default que diga "please_select_a_image", y aqu� mismo debe salir el error: "invalid image format""
                 -->
                 </div>
            <div id="browse">
            	<!--  <div id="browse_text"><input id="browse_location" type="text" />
                </div>  -->
                <div id="browse_button">
                <!--  <a href="#"></a>-->
               <!-- <img src="img/browse.png" alt="enter" width="127" height="26" style="position:absolute;"/><input type="file" name="image" style="opacity: 0;"/>  -->
               <input type="file" name="image">
               </div>
            </div>
            <div id="message">
            	<div id="message_header">
            		write_a_message:
                </div>
                <div id="message_container">
                	<textarea id="message_text" name="texto"></textarea>
                </div>
            </div>
            <div id="location">
            	<div id="location_latitude_box">
                	<input id="latitude" name="latitude" type="hidden" readonly="readonly" />
                </div>
                <div id="location_latitude">
                	
                </div>
                <div id="location_latitude_box">
                	<input id="longitude" name="longitude" type="hidden" readonly="readonly" />
                </div>
                <div id="location_longitude">
                	
                </div>
            	<div id="button_create_key">
            	<input type="image" id="botonCreateKey" src="./img/create.png" name="create" value ="create" height="26" width="195" border="0" vspace="0" alt="enter" tabindex="6" />
            	</div>
			</div>
		</div>
        <div id="gmap">
		</div>
		</form>
	</div>

	<?php include("./inc/pie.php"); ?>

</div>
</body>
</html>
