<?php 

require_once('config/config.php'); 
require_once('lib/class/Usuario.php'); 
require_once 'DAO.php';

/**
 * Clase principal del sistema
 * @author DISE3320 - 20092
 */
class NotAProfile{
	

//----------------------------------------------------------------------------------------------
// Constructor
//----------------------------------------------------------------------------------------------	
	
	/**
	 * Función constructora vacia
	 * @return No return
	 */
	function NotAProfile(){
		//vacio
	}
	


//----------------------------------------------------------------------------------------------
// Funciones relacionadas con el Registro/Login del sistema
//----------------------------------------------------------------------------------------------
	
	/**
	 * Función que procesa el inicio (login/registro) al sistema. 
	 * En caso de encontrar un email, clave y reclave se asume que se esta registrando.
	 * En caso de encontrar unicamente email y clave se asume que se esta logeando.
	 * @param $email
	 * @param $clave
	 * @param $reclave
	 * @return unknown_type
	 */
	public static function procesarInicio($email, $clave, $reclave = ''){
		if ($reclave == ''){
			validarUsuario($email, $clave);
		} else{
			registrarUsuario($email, $clave);
		}
	}
	
	/**
	 * Función que agrega un nuevo usuario al sistema.
	 * @param $email Email del nuevo usuario
	 * @param $clave Clave que aigna el usuario a su cuenta
	 * @param $reclave Confirmación de la clave
	 * @return no return
	 */
	public static function registrarUsuario($email, $clave){
		// verificar que el email no exista en la bd (llamar metodo)
		if(NotAProfile::existeUsuario($email)){
			return "Error, el usuario ya se encuentra registrado.";
			exit;
		}else{
			
			//ingresar a la base de datos el nuevo usuario, como inactivo, la clave entra como un md5 de si misma, 
			//esto debe tenerse en cuenta a la hora de hacer login.
			$fecha = date("c");
			$sql = sprintf("INSERT INTO usuario (email, clave, flag_activo, fecha_creado) VALUES ('%s','%s','%s','%s')",$email,md5($clave),0,$fecha);
			$exito = DAO::doSQL($sql);
			$id = DAO::lastId();
			if(!$exito){
				return "Error de la base de datos.";
			}else{
				// enviar email de confirmación (llamar metodo)
				NotAProfile::enviarEmailValidacion($email, $id);
				return "Success";
			}
		}
	}
	
	/**
	 * Función que hace login del usuario
	 * @param unknown_type $email
	 * @param unknown_type $clave
	 */
	public static function hacerLogin($email, $clave){
		global $app;
		$email = trim($email);
		$clave = trim($clave);

		/* Checks that email is in database and password is correct */
		$email = stripslashes($email);
		$result = NotAProfile::validarUsuario($email, md5($clave));
		
		/* Check error codes */
		if($result == 0){
			return false;
			exit;
		}
		
		 /* Username and password correct, register session variables */
		 $usuario  = NotAProfile::infoUsuario($email);
		 $_SESSION['username'] = $usuario[0]['email'];
		 $_SESSION['userid']   = $usuario[0]['id'];
		
		 return true;
		 
		  
		 echo "<br />".$_SESSION['username'];
		 echo "<br />".$_SESSION['userid'];
		if(isset($_SESSION['username']) && isset($_SESSION['userid']) && $_SESSION['username'] != ""){
			echo "Estan creadas las credenciales ";
			
		}
		else{
			echo "No se crearon credenciales";
		}
		
	}

	/**
	 * Función que verifica que el email exista y la clave esté bien
	 */
	public static function validarUsuario($email, $clave)
	{
		$sql = sprintf("SELECT * FROM usuario WHERE email= '%s'",$email);
		$usuario = DAO::doSQLAndReturn($sql);
		if(!isset($usuario[0]['id']))
		{
			return 0;
			exit;
		}
		else
		{
			if($usuario[0]['clave']==$clave)
			{
				return 1;
				exit;
			}
			else
			{
				return 0;
				exit;
			}
		}
	}
	
	/**
	 * Función que devuelve la información del usuario
	 */
	public static function infoUsuario($email)
	{
		$sql = sprintf("SELECT * FROM usuario WHERE email= '%s'",$email);
		return DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Función que verifica si un usuario representado con su email existe 
	 * o no en el sistema.
	 * @param $email
	 * @return boolean, true o false en caso de existir o no en el sistema. 
	 */
	 public static function existeUsuario($email){
		//TODO GOMEZ
		// verificar si un determinado email esta registrado en la BD
	   $resultados=array();
	   $resultados=DAO::doSQLAndReturn("SELECT email FROM usuario WHERE email = '%s'",$email);
       $largo= count($resultados);
 
                  
       // retorna true false dependiendo sea el caso
		//return 0;
       if($largo==0){
         return false;
       }else{
         return true;
       }
	}
	
	/**
	 * Función que envia a un usuario determinado un email de confirmación para 
	 * poder validar su cuenta y asignar valor "activo" a dicho usuario.
	 * @param $email
	 * @return No return
	 */
	public static function enviarEmailValidacion($email, $id){

		
		global $app;
		//Crea el código de activación usando como parametro el email y el tiempo el milisegundos 
		// con aumento de la entropia activado.
		$codigoUnico = md5(uniqid($email.mt_rand(), true));
		
		//ingresa a la base de datos el id relacionado al correo
		$sql = "UPDATE usuario SET id_activacion = $codigoUnico WHERE id = $id";
		DAO::doSQL($sql);
				
		// enviar correo con este codigo dentro de un link
		$link = $app['url'] ."a.php?c=". $codigoUnico;
		
		List ($nombre, $empresaEmail) = split("@", $email);
		$msg = "Hola $nombre!, \r\n\r\n
		
		Gracias por registrarte en not_a_profile!, en este momento eres un usuario inactivo, 
		para activar tu cuenta has clic en el siguiente link: \r\n\r\n
		
		$link \r\n\r\n
		
		Equipo not_a_profile \r\n
		{$app['url']}\r\n
		
		
		";
		NotAProfile::sendMail($email, $email, 'Activación en Not_A_Profile!', $msg);
	}
	
	/**
	 * Función que se encarga de verificar si el código unico existe en la base de datos, en caso de que sí
	 * exista activa el usuario relacionado con el código. 
	 * @param $codigoActivacion
	 * @return unknown_type
	 */
	public static function activarUsuario($codigoActivacion){
		$sql=sprintf("UPDATE usuario SET flag_activo = '1' WHERE id_activacion = '%s'",$codigoActivacion);
		$exito = DAO::doSQL($sql);
		return $exito;
	}
	
	

	/**
	 * Función que se encarga de cerrar la sesión de un usuario dado su email
	 * @param $email
	 * @return No return
	 */
	public static function cerrarSesion (){
		//Elimina la seción
		session_start(); 
		$_SESSION = array(); 
		session_destroy();

		
		//Redirecciona a la pagina principal
		//header ("Location: index.php");  
	}

	
	/**
	 * Función que revisa si el usuario está logeado
	 */
	public static function estaLogeado(){
			if(isset($_SESSION['userid']))
			{
				return true;
			}
			else
			{
				return false;
			}
	}
//----------------------------------------------------------------------------------------------
// Funciones relacionadas con la creación, reclamo y validación de llaves
//----------------------------------------------------------------------------------------------
	
	/** 
	 * Función que se encarga de crear una llave dados 3 parámetros básicos, latitud longitud y texto,
	 *  Esta función es provisional para las pruebas.
	 *  devuelve el código de la llave
	 *  @param $lat
	 *  @param $long
	 *  @param $texto
	 *  @return codigo, cadena de caracteres asociada a la llave. 
	 *  error en caso de no haber sido exitoso el proceso.
	 */	 
	 public static function crearLlave($lat, $long, $texto){
	 	global $app;
		$codigo = NotAProfile::elegirCodigoUnico();
		//El id del creador es siempre 1 para probar la creación de llaves.
		$creador_id= $_SESSION['userid'];
		if(!isset($creador_id)){echo("you need to be logged in");exit;}	
			$fecha = date("c");
			$sql = sprintf("INSERT INTO llave (txt,latitud,longitud,codigo,creador_id,fecha_creado) VALUES ('%s','%s','%s','%s','%s','%s')",
						$texto,
						$lat,
						$long,
						$codigo,
						$creador_id,
						$fecha
						);
			$exito = DAO::doSQL($sql);
			if($exito!=1)
			{
				$codigo="error";
				return $codigo;
				exit;
			}
	
			$url = $app['url']."llave.php?c=".$codigo;
			return $url;
	}
	
	
	/*
	 * Elige un código único para la llave
	 * 
	 */
	public static function elegirCodigoUnico(){
		$codigo= substr(md5(rand()), 5, 6);
		
		$sql = "SELECT codigo FROM llave";
		$cods= DAO::doSQLAndReturn($sql);
		$noesunico=1;
		while($noesunico==1)
		{
			$noesunico=0;
			for ($index = 0; $index < count($cods) && $noesunico==0; $index++) {
				if($filename==$cods[$index]['codigo'])
				{
					$noesunico=1;
				}				
			}
			$codigo = substr(md5(rand()), 5, 6);
		}	
		return $codigo;
	}
	
	
	/**
	 * Este metodo se encarga de marcar una llave como reclamada
	 * @param unknown_type $idLlave
	 * @param unknown_type $idUuario
	 * @return unknown_type
	 */
	public static function reclamarLlave($codigoLlave, $idUuario){
		$sql=sprintf("SELECT * FROM llave WHERE codigo='%s'",$codigoLlave);
		$llave = DAO::doSQLAndReturn($sql);
		if(!isset($llave[0]['id']))
		{
			return "Error, la llave no existe.";
			exit;
		}
		else if(isset($llave[0]['reclamador_id']))
		{
			return "Error, la llave ya ha sido reclamada";
			exit;
		}
		else
		{
			$fecha = date("c");
			$sql=sprintf("UPDATE llave SET reclamador_id = '%s',fecha_reclamado = '%s' WHERE codigo = '%s'",$idUuario,$fecha,$codigoLlave);
			DAO::doSQL($sql);
			return $llave;
		}
	}
	
	
	/**
	 * Este metodo se encarga de marcar una llave como aceptada, despues de haber sido reclamada
	 * @param unknown_type $idLlave
	 */
	public static function aceptarLlave($codigoLlave){
			$sql=sprintf("UPDATE llave SET flag_aceptado = 1 WHERE codigo = '%s'",$codigoLlave);
			$exito = DAO::doSQL($sql);
			return $exito;
	}
	/**
	 * Esta metodo se encarga de realizar una validación de llave
	 * @param unknown_type $idLlave
	 * @return unknown_type
	 */
	public static function validarLlave($idLlave){
		// TODO
	}
	
	/**
	 * Este método retorna todas las llaves que se encuentran disponibles (No han sido reclamadas)
	 * para ubicarlas dentro del mapa
	 * @return unknown_type
	 */
	public static function listaLlavesDisponibles(){
		$sql = "SELECT * FROM llave WHERE reclamador_id IS NULL";
		$llaves = DAO::doSQLAndReturn($sql);
		return $llaves;
	}
	
	
//----------------------------------------------------------------------------------------------
// Funciones auxiliares
//----------------------------------------------------------------------------------------------	

	/** Función que se encarga de modificar la foto para que esta tenga el formato estandar,
	 * la foto modificada reemplaza la original.
	 *  @param $urlFoto
	 *  @return boolean, true o false dependiendo de si la operación tuvo éxito o no.
	 */
	public static function modificarFoto($urlFoto){
		//TODO
	}
	
	/** Función que se encarga de crear un link para un id de una llave dada, 
	 *  devuelve el link creado.
	 *  @param $idLlave
	 *  @return link, cadena de caracteres asociada a la llave.
	 */
	public static function crearLink($idLlave){
		//TODO
	}
	
	/**
	 * Función que se encarga de enviar un email
	 * @param $to_name
	 * @param $to_email
	 * @param $subject
	 * @param $msg
	 * @return unknown_type
	 */
	public static function sendMail($to_name,$to_email,$subject,$msg) {
		/***************************
		include_once("Mail.php");
		
		$recipients = $app['siteemail'];
		
		$headers["From"]    = $app['siteemail'];
		$headers["To"]      = $to_email;
		$headers["Subject"] = $subject;
		$headers["Content-type"] = "text/plain; charset=utf-8";
		
		$body = $msg;
		
		$params["host"] = $app['smtp_host'];
		$params["port"] = $app['smtp_port'];
		$params["auth"] = $app['smtp_auth'];
		$params["username"] = $app['smtp_username'];
		$params["password"] = $app['smtp_password'];
		
		// Create the mail object using the Mail::factory method
		$mail_object =& Mail::factory("smtp", $params);
		
		return $mail_object->send($recipients, $headers, $body);
		***************************/
		global $app;
		$headers = "From: " . $app['siteemail'] . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
		$body = $msg;
		mail($to_email,$subject,$body,$headers);
	}
	
	


	
	

	
	

	
	
}


?>