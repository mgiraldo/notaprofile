<?php 
require_once('lib/class/classTextile.php');
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');
//echo("id=".$_SESSION['userid']);

$textile = new Textile();
$msg = "";
if(isset($_POST["latitud"])&&isset($_POST["longitud"]))
{
	if(($_POST["texto"]!="")||($_FILES['image']["name"]!=""))
	{
		$foto=NULL;
		if($_FILES['image']["name"]!="")
		{
			$foto = NotAProfile::subirFoto('image');	
		}
		
		//Texto enriquecido
		$texto = $textile->TextileThis($_POST["message_text"]);
		$codigo = NotAProfile::crearLlave($_POST["latitud"],$_POST["longitud"],$texto,$foto);
		
		
		if($codigo!="error")
		{
		 $msg = $codigo;
		}
		else
		{
		  $msg = "error_creating_key";
		}
		die($msg);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/estilosInsertarLlave.css" rel="stylesheet" type="text/css" />
<title>not_a_profile</title>
</head>
 <script src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $app['apiKey'] ?>&sensor=true"
            type="text/javascript"></script>
    <script type="text/javascript">
    var map;
    var llave;
    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("gmap"));
        map.setCenter(new GLatLng(4.620913,-74.083643), 13);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        GEvent.addListener(map, "click", function(overlay,latlng) {
				llave = latlng;
				document.getElementById("latitude").value = latlng.lat();
				document.getElementById("longitude").value = latlng.lng();
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
   <script>
      function preview(img,obj) {
         img.src = "file:///" + obj.value;
      }
   </script>

<body onload="initialize()" onunload="GUnload()">
<div id="contenido">

	<?php include("./inc/cabezote.php"); ?>
	<div id="cuerpo_sign">
		<form method="post" enctype="multipart/form-data">
		<div id="create_key">
            <div id="preview_image">
                <img id="img" width='270' height='138' src="./img/nofoto.jpg" />
                <!--
                el div tiene el tamaño indicado, la pregunta concretamente es sí se muestra un preview de la imagen al sacarla del browser,
                por lo que tocaría usar el convertidor de tamaños de imagen del que habla mauricio, o si simplemente no se muestra la imagen,
                también debe haber un default que diga "please_select_a_image", y aquí mismo debe salir el error: "invalid image format""
                 -->
                 </div>
            <div id="browse">
            	<!--  <div id="browse_text"><input id="browse_location" type="text" />
                </div>  -->
                <div id="browse_button">
                <!--  <a href="#"></a>-->
               <!-- <img src="img/browse.png" alt="enter" width="127" height="26" style="position:absolute;"/><input type="file" name="image" style="opacity: 0;"/>  -->
               <input type="file" name="image" onchange = "preview(document.forms[0].img, this)">
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
                	<input id="latitude" type="text" readonly="readonly"/>
                </div>
                <div id="location_latitude">
                	latitud:
                </div>
                <div id="location_latitude_box">
                	<input id="longitude" name="latitud" type="text" readonly="readonly"/>
                </div>
                <div id="location_longitude">
                	longitude:
                </div>
            	<div id="button_create_key">
            	<input type="image" id="botonCreateKey" name="longitud" src="./img/create.png" name="create" value ="create" height="26" width="195" border="0" vspace="0" alt="enter" tabindex="6" />
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
