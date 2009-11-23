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
    <?php
          for($i=0;$i<$count;$i++)
          {
    ?>
    var <?php echo("llave".$i."a")?>;
    <?php }?>
    
    function initialize() {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("gmap"));
		map.setCenter(new GLatLng(4.620913,-74.083643), 13);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        map.enableScrollWheelZoom();
      

          <?php
          	 for($i=0;$i<$count;$i++)
          	 {
          	 $fecha = new DateTime($llaves[$i]["fecha_creado"]);	
          	 $fechaFormato = $fecha->format('jS F Y');
          ?>

          var point<?php echo($i)?> = new GLatLng(<?php echo($llaves[$i]["latitud"]); ?>,<?php echo($llaves[$i]["longitud"]); ?>);
          var baseIcon<?php echo($i)?> = new GIcon(G_DEFAULT_ICON);
          baseIcon<?php echo($i)?>.image = "llave.png";
          var tamanio<?php echo($i)?> = 20;
          baseIcon<?php echo($i)?>.iconSize = new GSize(tamanio<?php echo($i)?>, tamanio<?php echo($i)?>+tamanio<?php echo($i)?>*0.7);
          baseIcon<?php echo($i)?>.shadowSize = new GSize(37, 34);
          baseIcon<?php echo($i)?>.iconAnchor = new GPoint(9, 34);
          baseIcon<?php echo($i)?>.infoWindowAnchor = new GPoint(9, 2);

          markerOptions = { icon:baseIcon<?php echo($i)?> };
          <?php echo("llave".$i."a")?> = new GMarker(point<?php echo($i)?>,markerOptions);
          GEvent.addListener(<?php echo("llave".$i."a")?>, "click", function() {
        	  <?php echo("llave".$i."a")?>.openInfoWindowHtml(" key_left  : " + "<?php echo($fechaFormato); ?>");
            });

        map.addOverlay(<?php echo("llave".$i."a")?>);
        <?php 
        	}
         ?>
      }
    }
      <?php
           	 for($i=0;$i<$count;$i++)
           	 {
           	 $fecha = new DateTime($llaves[$i]["fecha_creado"]);	
           	 $fechaFormato = $fecha->format('jS F Y');
           ?>
      function centrarEnPunto<?php echo("llave".$i."a")?>()
		        {
		            
		        	map.setCenter(new GLatLng(<?php echo($llaves[$i]["latitud"])?>,<?php echo($llaves[$i]["longitud"]); ?>));
		        	<?php echo("llave".$i."a")?>.openInfoWindowHtml(" key_left  : " + "<?php echo($fechaFormato); ?>");
		        	document.getElementById(ida).setAttribute("class","activa");
		    		alert("document.getElementById("+<?php echo("llave".$i."a")?>+").setAttribute(\"class\",\"activa\");");
		    		//document.getElementById(ida).setAttribute("className","activa");
		        } 
      <?php 
         	}
          ?>
    



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
				<li id=<?php echo("llave".$index."a")?>>
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
						<a href="#" onclick="centrarEnPunto<?php echo("llave".$index."a")?>();">view</a>
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
