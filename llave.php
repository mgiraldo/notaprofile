<?php 
require_once 'lib/class/NotAProfile.php';
if(isset($_GET['c']))
{
	if(isset($_POST['gusta']))
	{
		NotAProfile::aceptarLlave($_GET['c']);
		echo("Ok");
?>	
<br>	
<a href="notprofile.php">back</a>
<?php 
	}
	else
	{
		$creador_id= $_SESSION['userid'];
		if(!isset($_SESSION['userid'])){echo("you need to be logged in");exit;}
		$llave = NotAProfile::reclamarLlave($_GET['c'],$creador_id);
		if(isset($llave[0]['txt']))
		{
			echo($llave[0]['txt']);
			echo($llave[0]['url']);
?>			
<form action="#" method="post"><input name="gusta" type="submit" value="like it"></form>
<br>
<form action="notprofile.php" method="post"><input name="nogusta" type="submit" value="don't like it"></form>
<?php			
		}
		else
		{
			echo($llave);
?>	
<br>
<a href="notprofile.php">back</a>
<?php 		
		}
	}
}
?>


