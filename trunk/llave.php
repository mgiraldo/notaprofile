<?php 
require_once 'lib/class/NotAProfile.php';
if(!NotAProfile::estaLogeado())
{
	?>
	<head>
	<script type="text/javascript" src="lib/javascript/jquery.js"></script>
	<script type="text/javascript" src="lib/javascript/thickbox.js"></script>
	<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
	<script> 
	$(document).ready(function(){ 
	tb_show("","index.php?tb=1&reclamollave=<?php echo($_GET['c']) ?>&KeepThis=true&TB_iframe=true&height=400&width=600&modal=true",""); 
	}); 
	</script>
	</head>
	<body>
	</body>
	<?php 
}
if(isset($_GET['c']))
{
	if(isset($_POST['gusta']))
	{
		NotAProfile::aceptarLlave($_GET['c']);
		echo("Ok");
		echo("<br>");
		echo($llave);
?>	
<br>
<img src="<?php echo ($app['url'].$app['photoroot'].$llave[0]['foto'].".jpg") ?>"></img>
	
<a href="notprofile.php">back</a>
<?php 
	}
	else
	{
		$creador_id= $_SESSION['userid'];
		$llave = NotAProfile::reclamarLlave($_GET['c'],$creador_id);
		if(isset($llave[0]['txt']))
		{
			echo($llave[0]['txt']);
?>	
<img src="<?php echo ($app['url'].$app['photoroot'].$llave[0]['foto'].".jpg") ?>"></img>		
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
else
{

}
?>


