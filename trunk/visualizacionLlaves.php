<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');


$filtro = $_GET['filtro'];
$llave = $_GET['llave'];
//Cuando filtro es 1 se muestran las claimed, sino, las orphans.
if(!isset($filtro))
{
	$filtro=0;
}

if($filtro==0)
{
	$llaves = NotAProfile::darLlavesDisponibles();
}
else if($filtro==1)
{
	$llaves = NotAProfile::darLlavesReclamadas2();
}

$count = count($llaves);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/estilos.css" rel="stylesheet" type="text/css" />
<title>not_a_profile</title>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo($app["apiKey"]); ?>&sensor=true"
            type="text/javascript"></script>
    <script type="text/javascript">
    var map;

    function centrarEnPunto(lat, lang, id)
    {
        var ida= "llave"+id;
    	map.setCenter(new GLatLng(lat,lang));
    	document.getElementById(ida).setAttribute("class","activa");
		alert("document.getElementById("+ida+").setAttribute(\"class\",\"activa\");");
    	//document.getElementById(ida).setAttribute("className","activa");
    }
    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("gmap"));
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
             marker.openInfoWindowHtml(" key_left  : " + fecha);
           });
         return marker;
    }

    </script>
</head>

<body onload="initialize()" onunload="GUnload()">
<div id="contenido">

	<div id="cabezote">
		<h1>not_a_profile</h1>
		<ul id="nav">
			<li><a href="#">keyring</a></li>
			<li><a href="#">connections</a></li>
		</ul>
		<div id="userinfo">
			rank: locksmith | <a href="#">log_out</a>
		</div>
	</div>
	
	<div id="cuerpo">
		<div id="llaves">
			<!--
			cambiar el class segun el tab activo:
			orphans: orphansactiva
			claimed: claimedactiva
			class = dinámico
			-->
			<?php if($filtro==1){$class ="claimedactiva";}else{$class ="orphansactiva";}?>
			<div id="mapatabs" class=<?php echo($class); ?>>
				<div class="orphans"><a href="?filtro=0">orphans</a></div>
				<div class="claimed"><a href="?filtro=1">claimed</a></div>
			</div>
			<ul id="listallaves">
			<?php for ($index = 0; $index < $count; $index++) {?>
			<!-- inicio llave tipo -->
				<li>
					<div class="fotollave">
					<!-- si la llave es mia o yo la reclame me sale la foto -->
					<!-- si no es mia o no la reclame me sale generica -->
					<?php if(NotAProfile::puedeSerVista($llaves[$index]))
					{?>
						
						<img src=<?php if(isset($llaves[$index]['foto'])){echo("photos/".$llaves[$index]['foto']."_t.jpg");}else{echo("img/fotogenerica.gif");}?> width="61" height="61" />
					<?php }else{?>
						<img src="img/fotogenerica.gif" width="61" height="61" />					
					<?php }?>
					</div>
					<div class="textollave">
						<!-- esto sale si la llave es MIA o YO LA RECLAME -->
						<?php if(NotAProfile::puedeSerVista($llaves[$index]))
						{?>
						<span class="texto"><?php echo($llaves[$index]['txt'])?></span><br />
						<span class="loc"><?php echo($llaves[$index]['latitud'].",".$llaves[$index]['longitud'])?> </span><br />
						<?php }?>
						<a href="#" onclick="centrarEnPunto(<?php echo($llaves[$index]['latitud'].",".$llaves[$index]['longitud'].",".$index) ?>)">view</a>
					</div>
				</li>
			<!-- fin llave tipo -->
			<?php }?>
			</ul>
		</div>
		<div id="mapa">
			<div id="navmapa">
				<a class="createkey" href="insertarLlave.php"><span>create_a_key</span></a>
			</div>
			<div id="gmap">
				
			</div>
		</div>
	</div>
	
	<div id="pie">
		<a href="#">help</a> | <a href="#">about this project</a> | <a href="#">google code</a> | <a href="#">copyright notice</a>
	</div>

</div>
</body>
</html>
