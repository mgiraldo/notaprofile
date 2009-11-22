<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');
echo("Bienvenido ".$_SESSION['username']);
if(isset($_GET['tb']))
{
?>	
	<script>
	self.parent.tb_remove();
	</script>	
<?php 
}
?>
<br>
<b>Yours:</b>
<br>
<br>
<?php 
//TODO llamar al método que devuelve las llaves creadas por esta persona.

	$llaves = NotAProfile::darLlavesCreadasReclamadas();
 	$numLlaves = count($llaves);
 	echo("claimed: $numLlaves <br>");

 	echo "<table border=\"1\">";
 	echo "<tr><th>id</th>";
	echo "<th>codigo</th>";
	echo "<th>txt</th>";
	echo "<th>foto</th>";
	echo "<th>fecha_creado</th>";
	echo "<th>latitud/longitud</th>";
	echo "<th>fecha_reclamado</th></tr>";
 	foreach ($llaves as &$llave) {
	 	echo "<tr><td>".$llave['id']."</td>";
		echo "<td>".$llave['codigo']."</td>";
		echo "<td>".$llave['txt']."</td>";
		echo "<td>".$llave['foto']."</td>";
		echo "<td>".$llave['fecha_creado']."</td>";
		echo "<td>".$llave['latitud']."/".$llave['longitud']."</td>";
		echo "<td>".$llave['fecha_reclamado']."</td></tr>";
 	}
 	echo "</table><br /><br />";
 	
 	
 	
 	echo "<tr><table border=\"1\">";
 	echo "<th>id</th>";
	echo "<th>codigo</th>";
	echo "<th>txt</th>";
	echo "<th>foto</th>";
	echo "<th>fecha_creado</th>";
	echo "<th>latitud/longitud</th>";
	echo "<th>fecha_reclamado</th></tr>";
	$llaves = NotAProfile::darLlavesCreadasNoReclamadas();
	$numLlaves = count($llaves);
 	echo("Unclaimed: $numLlaves <br>");
 	foreach ($llaves as &$llave) {
 		echo "<tr><td>".$llave['id']."</td>";
		echo "<td>".$llave['codigo']."</td>";
		echo "<td>".$llave['txt']."</td>";
		echo "<td>".$llave['foto']."</td>";
		echo "<td>".$llave['fecha_creado']."</td>";
		echo "<td>".$llave['latitud']."/".$llave['longitud']."</td>";
		echo "<td>".$llave['fecha_reclamado']."</td></tr>";
 	}
 	echo "</table><br /><br />";
 	
 	
 	echo "<tr><table border=\"1\">";
 	echo "<th>id</th>";
	echo "<th>codigo</th>";
	echo "<th>txt</th>";
	echo "<th>foto</th>";
	echo "<th>fecha_creado</th>";
	echo "<th>latitud/longitud</th>";
	echo "<th>fecha_reclamado</th></tr>";
 	$llaves = NotAProfile::darLlavesCreadasVencidas();
 	$numLlaves = count($llaves);
 	echo("Expired: $numLlaves <br>");
 	foreach ($llaves as &$llave) {
 		echo "<tr><td>".$llave['id']."</td>";
		echo "<td>".$llave['codigo']."</td>";
		echo "<td>".$llave['txt']."</td>";
		echo "<td>".$llave['foto']."</td>";
		echo "<td>".$llave['fecha_creado']."</td>";
		echo "<td>".$llave['latitud']."/".$llave['longitud']."</td>";
		echo "<td>".$llave['fecha_reclamado']."</td><tr/>";
 	}
 	echo "</table><br /><br />";
?>
<br>
<br>
<b>Theirs:</b>
<br>
<br>
<?php 
//TODO llamar al método que devuelve las llaves reclamadas por esta persona.
/**
$llaves = NotAProfile::darLlavesReclamadas();
 echo("claimed: " . count($llaves)."<br>");
$llaves = NotAProfile::darLlavesDisponibles();
 echo("key_hunt: " . count($llaves)."<br>");
 $llaves = NotAProfile::darLlavesDisponiblesContactos();
 echo("my_not_profilers: " . count($llaves)."<br>");
 **/
?>
<br>
<br>
<br>
<a href="insertarLlave.php">create_key</a>
<br>
<a href="visualizacionLlaves.php">check_out_keys</a>
<br>
<br>
<br>
<a href="home.php?logout=si">log_out</a>