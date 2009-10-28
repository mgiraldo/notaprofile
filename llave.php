<?php 
require_once 'lib/class/NotAProfile.php';
if(isset($_GET['c']))
{
	if(isset($_POST['gusta']))
	{
		NotAProfile::aceptarLlave($_GET['c']);
		echo("Ok");
?>		
<a href="notprofile.php">back</a>
<?php 
	}
	else
	{
		$llave = NotAProfile::reclamarLlave($_GET['c'],5);
		if(isset($llave[0]['txt']))
		{
			echo($llave[0]['txt']);
			echo($llave[0]['url']);
?>			
<form action="#" method="post"><input name="gusta" type="submit" value="like it"></form>
<br>
<form action="#" method="post"><input name="nogusta" type="submit" value="don't like it"></form>
<?php			
		}
		else
		{
			echo($llave);
		}
	}
}
?>


