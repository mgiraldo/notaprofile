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
	 * Funci�n constructora vacia
	 * @return No return
	 */
	function NotAProfile(){
		//vacio
	}
	


//----------------------------------------------------------------------------------------------
// Funciones relacionadas con el Registro/Login del sistema
//----------------------------------------------------------------------------------------------
	
	/**
	 * Funci�n que procesa el inicio (login/registro) al sistema. 
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
	 * Funci�n que agrega un nuevo usuario al sistema.
	 * @param $email Email del nuevo usuario
	 * @param $clave Clave que aigna el usuario a su cuenta
	 * @param $reclave Confirmaci�n de la clave
	 * @return no return
	 */
	public static function registrarUsuario($email, $clave){
		// verificar que el email no exista en la bd (llamar metodo)
		if(NotAProfile::existeUsuario($email)==true){
			return "Error";
			exit;
		}else{
			
			//ingresar a la base de datos el nuevo usuario, como inactivo, la clave entra como un md5 de si misma, 
			//esto debe tenerse en cuenta a la hora de hacer login.
			$sql = sprintf("INSERT INTO usuario (email, clave, flag_activo) VALUES ('%s','%s','%s')",$email,md5($clave),0);
			$exito = DAO::doSQL($sql);
			if(!$exito){
				return "Error";
			}else{
				// enviar email de confirmaci�n (llamar metodo)
				NotAProfile::enviarEmailValidacion($email);
				return "Success";
			}
		}
	}
	

	
	
	
	
	/**
	 * Funci�n que verifica si un usuario representado con su email existe 
	 * o no en el sistema.
	 * @param $email
	 * @return boolean, true o false en caso de existir o no en el sistema. 
	 */
	 public static function existeUsuario($email){
		//TODO GOMEZ
		// verificar si un determinado email esta registrado en la BD
	   $resultados=array();
	   $resultados=DAO::doSQLAndReturn("SELECT email FROM usuario");

       $existe=0;
       $largo= count($resultados);

       for($i=0;$i<$largo;$i++){

         if($email==$resultados[$i]['email']){
      
           $existe=1;
         }
	 	
       }
           
       // retorna true false dependiendo sea el caso
		//return 0;
       if($existe==1){
         return true;
       }else{
         return false;
       }
	}
	
	/**
	 * Funci�n que envia a un usuario determinado un email de confirmaci�n para 
	 * poder validar su cuenta y asignar valor "activo" a dicho usuario.
	 * @param $email
	 * @return No return
	 */
	public static function enviarEmailValidacion($email){
		//TODO GUEVARA corregir
		//Crea el c�digo de activaci�n usando como parametro el email y el tiempo el milisegundos 
		// con aumento de la entropia activado.
		$codigoUnico = md5(uniqid($email.mt_rand(), true));
		
		//ingresa a la base de datos el id relacionado al correo
		$sql = "UPDATE usuario SET id_activacion = '$codigoUnico' WHERE email = '$email'";
		DAO::doSQL($sql);
				
		// enviar correo con este codigo dentro de un link
		$link = $app['url'] . $codigoUnico;
		
		List ($nombre, $empresaEmail) = split("@", $email);
		$msg = "Hola '$nombre'!, \r\n\r\n
		
		Gracias por registrarte en Not_A_Profile!, en este momento eres un usuario inactivo, 
		para activar tu cuenta has clic en el siguiente link: \r\n\r\n
		
		'$link' \r\n\r\n
		
		Equipo Not_A_Profile \r\n\r\n
		
		";
		NotAProfile::sendMail($email, $email, 'Activaci�n en Not_A_Profile!', $msg);
	}
	
	/**
	 * Funci�n que se encarga de verificar si el c�digo unico existe en la base de datos, en caso de que s�
	 * exista activa el usuario relacionado con el c�digo. 
	 * @param $codigoActivacion
	 * @return unknown_type
	 */
	public static function activarUsuario($codigoActivacion){
		$sql=sprintf("UPDATE usuario SET flag_activo = '1' WHERE token_validacion = '%s'",$codigoActivacion);
		$exito = DAO::doSQL($sql);
		return $exito;
	}
	
	/**
	 * Funci�n que se encarga de cerrar la sesi�n de un usuario dado su email
	 * @param $email
	 * @return No return
	 */
	public static function cerrarSesion ($email){
		//TODO GUEVARA revisar
		//Elimina la seci�n
		session_start(); 
		$_SESSION = array(); 
		session_destroy();
		session_start(); 
		
		//Redirecciona a la pagina principal
		header ("Location: index.php");  
	}

//----------------------------------------------------------------------------------------------
// Funciones relacionadas con la creaci�n, reclamo y validaci�n de llaves
//----------------------------------------------------------------------------------------------
	
	/** 
	 * Funci�n que se encarga de crear una llave dados 3 par�metros b�sicos, latitud longitud y texto,
	 *  Esta funci�n es provisional para las pruebas.
	 *  devuelve el c�digo de la llave
	 *  @param $lat
	 *  @param $long
	 *  @param $texto
	 *  @return codigo, cadena de caracteres asociada a la llave. 
	 *  error en caso de no haber sido exitoso el proceso.
	 */
	public static function crearLlavee(){
		
	}
	
	
	/**
	 * Este metodo se encarga de crear una llave asociada a un usuario
	 * @param unknown_type $idUsuario
	 * @param unknown_type $texto
	 * @param unknown_type $lat
	 * @param unknown_type $long
	 * @return unknown_type
	 */
	public static function crearLlave2($idUsuario, $texto, $lat, $long ){
		// TODO
	} 
	
	/**
	 * 	ESTE ES EL CODIGO QUE SE TENIA PARA LA CREACI�N DE LLAVES
	 * 
	 *
	 */
	 
	 public static function crearLlave($lat, $long, $texto){
		$codigo = substr(md5(rand()), 5, 6); 
		//El id del creador es siempre 1 para probar la creaci�n de llaves.
		$creador_id= 1;
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
		}
		return $codigo;
	}
	
	
	
	
	
	/**
	 * Este metodo se encarga de marcar una llave como reclamada
	 * @param unknown_type $idLlave
	 * @param unknown_type $idUuario
	 * @return unknown_type
	 */
	public static function reclamarLlave($idLlave, $idUuario){
		// TODO
	}
	
	/**
	 * Esta metodo se encarga de realizar una validaci�n de llave
	 * @param unknown_type $idLlave
	 * @return unknown_type
	 */
	public static function validarLlave($idLlave){
		// TODO
	}
	
	/**
	 * Este m�todo retorna todas las llaves que se encuentran disponibles (No han sido reclamadas)
	 * para ubicarlas dentro del mapa
	 * @return unknown_type
	 */
	public static function listaLlavesDisponibles(){
		$sql = "SELECT * FROM llave";
		$llaves = DAO::doSQLAndReturn($sql);
		return $llaves;
	}
	
	
//----------------------------------------------------------------------------------------------
// Funciones auxiliares
//----------------------------------------------------------------------------------------------	

	/** Funci�n que se encarga de modificar la foto para que esta tenga el formato estandar,
	 * la foto modificada reemplaza la original.
	 *  @param $urlFoto
	 *  @return boolean, true o false dependiendo de si la operaci�n tuvo �xito o no.
	 */
	public static function modificarFoto($urlFoto){
		//TODO
	}
	
	/** Funci�n que se encarga de crear un link para un id de una llave dada, 
	 *  devuelve el link creado.
	 *  @param $idLlave
	 *  @return link, cadena de caracteres asociada a la llave.
	 */
	public static function crearLink($idLlave){
		//TODO
	}
	
	/**
	 * Funci�n que se encarga de enviar un email
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