<?php
require_once 'lib/class/NotAProfile.php';

                $idllave = $_GET['c'];
                $llave = NotAProfile::darLlave($_GET['c']);
                $texto = $llave[0]['txt']; 
                $primerosCaracteres = $primerosCaracteres = substr($texto, 0, 100) . '...';
                $primerosCaracteres = strip_tags($primerosCaracteres);
                if(isset($_GET['like']))
                {       $gusta = $_GET['like'];
                        if($gusta == 1){
                                NotAProfile::aceptarLlave($idllave);
                                header( 'Location: ' . '/key/'.$idllave);
                        }
                        else{
                                NotAProfile::rechazarLlave($idllave);
                                header( 'Location: ' . '/key/'.$idllave);
                        }
                }
                
                if(NotAProfile::estaLogeado() && ($llave[0]['creador_id'] != $_SESSION['userid']))
                {
                        $creador_id= $_SESSION['userid'];
                        $recibo = NotAProfile::reclamarLlave($_GET['c'],$creador_id);   
                }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/css/estilos.css" rel="stylesheet" type="text/css" />
<title>not_a_profile: <?php echo $primerosCaracteres ?></title>
<style>
#fotollave {
        <?php 
        if($llave[0]['reclamador_id'] == "" || $llave[0]['reclamador_id'] == $_SESSION['userid'] || $llave[0]['creador_id'] == $_SESSION['userid']){ ?>
                background: url(/<?php echo ($app['photoroot'].$llave[0]['foto'].".jpg") ?>) repeat-x;
        <?php }
        else{ ?>
                background: url(/img/fotoejemplo.jpg) repeat-x;
        <?php } ?>              
}
</style>

        <?php if(!NotAProfile::estaLogeado()){ 
        	header("Location: /index.php?r=/key/".$_GET['c']);
        	?>
                        
		<!--  
		<script type="text/javascript" src="lib/javascript/jquery.js"></script>
		<script type="text/javascript" src="lib/javascript/thickbox.js"></script>
		<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
		<script> 
		        $(document).ready(function(){ 
		        tb_show("","index.php?tb=1&reclamollave=<?php echo($_GET['c']) ?>&KeepThis=true&TB_iframe=true&height=400&width=600&modal=true",""); 
		        }); 
		</script>
		            -->    
        <?php }?>

</head>

<body>
<div id="fotollave">
        <div id="gradiente"> </div>
</div>
<div id="contenido">

        <?php
        if($llave[0]['flag_aceptado'] == "0"  && $llave[0]['creador_id'] != $_SESSION['userid']){ 
                include("./inc/cabezotellave.php");
        }
        else{
                //Cambiar cuando todos los link esten perfectos a:
                include("./inc/cabezote.php");
                //include("./inc/cabezotellave.php");
        }       
        ?>      
        
        <div id="cuerpo">
                <?php 
                if($llave[0]['reclamador_id'] == "" || $llave[0]['reclamador_id'] == $_SESSION['userid'] || $llave[0]['creador_id'] == $_SESSION['userid']){
                        if($llave[0]['flag_aceptado'] == "0" && ($llave[0]['creador_id'] != $_SESSION['userid'])) {?>
                        <div id="dislike"><a href="<?php echo $llave[0]['codigo']?>/-1"><span>dislike</span></a></div>
                        <?php }?>
                
                        <div id="textollave">
                        <?php if($llave[0]['creador_id'] == $_SESSION['userid']){?>
                        <?php echo("www.notaprofile.com/key/".$_GET['c']);?>
                        <?php }?>
                        <?php echo($llave[0]['txt']);?>
                        </div>
                
                        <?php if($llave[0]['flag_aceptado'] == "0" && ($llave[0]['creador_id'] != $_SESSION['userid'])) {?>
                        <div id="like"><a href="<?php echo $llave[0]['codigo']?>/1"><span>like</span></a></div>
                <?php }
                }
                else{?>
                        <div id="textollave">
                                Ups!
                                the key has already been claimed
                        </div>
                <?php } ?>
        </div>
        <?php include("./inc/pie.php");?>
</div>
</body>
</html>
