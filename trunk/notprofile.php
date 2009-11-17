<?php 
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');
echo("Bienvenido ".$_SESSION['username']);
if(isset($_GET['tb']))
{
?>	
	<script>
	alert("hey");
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
/**
$llaves = NotAProfile::darLlavesCreadasReclamadas();
 echo("claimed: " . count($llaves)."<br>");
$llaves = NotAProfile::darLlavesCreadasNoReclamadas();
 echo("unclaimed: " . count($llaves)."<br>");
 $llaves = NotAProfile::darLlavesCreadasVencidas();
 echo("expired: " . count($llaves)."<br>");
**/
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