<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');

if(isset($_GET['tb']))
{
?>	
	<script>
	self.parent.tb_remove();
	</script>	
<?php 
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>not_a_profile</title>
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Pagina de prueba!! </h1><br /><br />
<?php echo("Bienvenido ".$_SESSION['username']); ?>
<br>
<br /> Nota: Coloque estas tablas para ver las llaves, obviamente no van en el dise�o final! MV<br /> 
<b>Yours:</b>
<br><br>
<?php 
//TODO llamar al m�todo que devuelve las llaves creadas por esta persona.

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
		echo "<td>".substr($llave['txt'],0,20)."".(strlen($llave['txt'])>=20? "...": "")."</td>";
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
		echo "<td>".substr($llave['txt'],0,20)."".(strlen($llave['txt'])>=20? "...": "")."</td>";
		echo "<td>".$llave['foto']."</td>";
		echo "<td>".$llave['fecha_creado']."</td>";
		echo "<td>".$llave['latitud']."/".$llave['longitud']."</td>";
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
		echo "<td>".substr($llave['txt'],0,20)."".(strlen($llave['txt'])>=20? "...": "")."</td>";
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
//TODO llamar al m�todo que devuelve las llaves reclamadas por esta persona.

	echo "<tr><table border=\"1\">";
 	echo "<th>id</th>";
	echo "<th>codigo</th>";
	echo "<th>txt</th>";
	echo "<th>foto</th>";
	echo "<th>fecha_creado</th>";
	echo "<th>latitud/longitud</th>";
	echo "<th>fecha_reclamado</th></tr>";
 	$llaves = NotAProfile::darLlavesReclamadas();
 	$numLlaves = count($llaves);
 	echo("keys that you claimed:: $numLlaves <br>");
 	foreach ($llaves as &$llave) {
 		echo "<tr><td>".$llave['id']."</td>";
		echo "<td>".$llave['codigo']."</td>";
		echo "<td>".substr($llave['txt'],0,20)."</td>";
		echo "<td>".$llave['foto']."</td>";
		echo "<td>".$llave['fecha_creado']."</td>";
		echo "<td>".$llave['latitud']."/".$llave['longitud']."</td>";
		echo "<td>".$llave['fecha_reclamado']."</td><tr/>";
 	}
 	echo "</table><br /><br />";
 	
 	$llaves = NotAProfile::darLlavesDisponibles();
 echo("key_hunt: " . count($llaves)."<br>");
 $llaves = NotAProfile::contarLlavesDisponibles();
 echo("my_not_profilers: " .($llaves)."<br>");
?>
<br>
<br>
<br>
<a href="/create">create_key</a>
<br>
<a href="/view">check_out_keys</a>
<br>
<br>
<br>
<a href="/logout">log_out</a>

<br /><br />
</body>
</html>
