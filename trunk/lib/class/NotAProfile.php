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
	 * Función que agrega un nuevo usuario al sistema.
	 * @param $email Email del nuevo usuario
	 * @param $clave Clave que aigna el usuario a su cuenta
	 * @param $reclave Confirmación de la clave
	 * @return  
	 *   1 - Alguno de los parametros se encuentra en blanco
	 *   2 - El campo del email no tiene el formato correcto
	 *   3 - Las contraseñas no coinciden
	 *   4 - El email ingresado ya esta asociado a un registro
	 *   5 - Problema ingresando datos a la base de datos
	 */
	public static function registrarUsuario($email, $clave, $clave2){
		
		$email = trim($email);
		$clave = trim($clave);
		$clave2 = trim($clave2);
		
		// Valida que no se tenga un campo vacio
		if($email=="" || $clave=="" || $clave2==""){return 1;}
		
		// Valida que el email ingresado tenga un formato valido
		$regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|museum|name))/";
		if(!(preg_match($regex,$email))){return 2;}
		
		// Verfica que las dos contraseñas sean iguales
		if($clave2!=$clave){return 3;}
		
		//Agrega caracteres de control XSS
		$email = strip_tags(addslashes(htmlspecialchars(htmlentities($email))));
		$clave = strip_tags(addslashes(htmlspecialchars(htmlentities($clave))));
		
		// Verifica que no exista un usuario con ese email registrado
		
		
		// verificar que el email no exista en la bd (llamar metodo)
		if(NotAProfile::existeUsuario($email)){
			return 4;
		}

		// Ingresa a la base de datos el nuevo usuario.
		$sql = "INSERT INTO usuario (id, email, clave, flag_activo, fecha_creado) VALUES (NULL, '$email', '".md5($clave)."', '0', NOW() )";
		if(DAO::doSQL($sql)){
			// Enviar email
			// $id = DAO::lastId();
			// NotAProfile::enviarEmailValidacion($email, $id);
			return 0;
		}else{
			return 5;
		}
	}
	
	/**
	 * Función que hace login del usuario
	 * @param unknown_type $email
	 * @param unknown_type $clave
	 * 
	 * Errores:
	 *   1 - En email o la contraseña se encuentran en blanco
	 *   2 - El campo del email no tiene el formato correcto
	 *   3 - El email ingresado no existe o la contraseña no coincide
	 */
	public static function login($email, $clave){

		$email = trim($email);
		$clave = trim($clave);
		
		// Valida que no se tenga un campo vacio
		if($email=="" || $clave==""){return 1;}
		
		// Valida que el email ingresado tenga un formato valido
		$regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.(([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|museum|name))/";
		if(!(preg_match($regex,$email))){return 2;}
		
		//Agrega caracteres de control XSS
		$email = strip_tags(addslashes(htmlspecialchars(htmlentities($email))));
		$clave = strip_tags(addslashes(htmlspecialchars(htmlentities($clave))));
		
		// Verifica que exista un registro asociado a un emai, y si existe, que la coincida la cllave
		if(!(NotAProfile::validarUsuario($email, md5($clave)))){return 3;}

		// Crea la sesión con las variables username y id
		$usuario = NotAProfile::infoUsuario($email);
		$_SESSION['username'] = $usuario[0]['email'];
		$_SESSION['userid']   = $usuario[0]['id'];
		
		return 0;
	}

	/**
	 * Esta función se encarga de validar que exista un usuario dentro de la base de datos
	 * con el email ingresado por parámetro. Si este registro existe, valida que la clave
	 * asociada conicida con la clave ingresada por parámetro
	 * @param unknown_type $email  - Email de un usuario
	 * @param unknown_type $clave  - Constraseña asociada al email 
	 * @return unknown_type - True si el usuario con $email existe y la coincide la clave,
	 *                        false de lo contrario
	 */
	public static function validarUsuario($email, $clave)
	{
		$sql = "SELECT clave FROM usuario WHERE email='$email'";
		$usuario = DAO::doSQLAndReturn($sql);
		if(count($usuario)==1)
			// Existe un único registro asociado al email
			return $usuario[0]['clave']==$clave?true:false;
		else 
			// No hay un registro asosciado a dicho email
			return false;
	}
	
	/**
	 * Esta función se encarga de regresar un vector con toda la información de un usario
	 * identificado con una direccion de email que ingresa por parámetro
	 * @param unknown_type $email - Email de un usuario
	 * @return unknown_type - Vector con los datos de tabla usuario de BD
	 */
	public static function infoUsuario($email)
	{
		$sql = "SELECT * FROM usuario WHERE email= '$email'";
		return DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Función que verifica si un usuario representado con su email existe 
	 * o no en el sistema.
	 * @param $email
	 * @return boolean, true o false en caso de existir o no en el sistema. 
	 */
	 public static function existeUsuario($email){
		
	   $resultados=DAO::doSQLAndReturn("SELECT count(*) as Contador FROM usuario WHERE email='$email'");
	   return $resultados[0]["Contador"]==0?false:true;
	   
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