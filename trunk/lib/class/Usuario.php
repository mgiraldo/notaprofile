<?php

require_once('DAO.php');

/**
 * Esta clase se encarga del manejo de las acciones asociadas a los usuarios dentro del sistema
 * @author WEB: Proyectos Experimentales (DISE)
 */
class Usuario {
	
	public static function addUsser($email, $pass){
		
	}
	
	
	/**
	 * Este metodo se encarga de realizar la consulta para obtener todos los usuarios
	 * que se encuentran registrados en el sistema
	 * @return unknown_type
	 */
	public static function getAll(){
		return DAO::doSQLAndReturn( "SELECT * FROM usuario" );
		// TODO Validar si se desea obtener tambien los usuarios que no han validado su cuenta con el correo
	}
	
	
	/**
	 * Esta funcion se encarga de rotornar el usuario que posee una dirección
	 * @param unknown_type $email
	 * @return unknown_type
	 */
	public static function findByEmail($email) {
		$sql = 'SELECT * FROM usuario WHERE email = ' . DAO::escape_str($email);
		$r = DAO::doSQLAndReturn($sql);
		if (count($r) == 0) {
			return false;
		}
		return $r[0];
	}
	
}




/*
function Usuario(){
	}

	function save( $email, $clave, $id = null ){
		if ($id==NULL) {
			$sql = sprintf("INSERT INTO usuario ( email, clave ) VALUES ( '%s','%s' )", DAO::escape_str($email), md5($clave) );
		} else {
			// solo cambiar si la clave cambia
			// sacar la clave de la base de datos
			// comparar con la clave que viene
			$sql = sprintf("UPDATE usuario SET email='%s', clave='%s' WHERE id = %d", DAO::escape_str($email), md5($clave), $id );
		}
		$r = DAO::doSQL($sql);
		if ($id==NULL) $r = DAO::lastId();
		return $r;
	}

	function getAll(){
		
	}
	
	
	
	function sendActivationEmail ($email) {
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			// email chimbo
			return -1;
		}
		$data = $this->findByEmail($email);
		if (!$data) {
			// email no existe
			return -2;
		}
		// mandar el mail
		$id = $data['id'];
		$email = $data['email'];
		$link = "{$app['url']}activar.php?". md5($id) . "-" . $token . "/";
		$tema = "Activa tu cuenta en not_a_profile";
		$contenido = "Hola!\r\n\r\n";
		$contenido .= "Te registraste en not_a_profile!\r\n\r\n";
		$contenido .= "Para activar tu cuenta debes hacer clic en el link a continuacion:";
		$contenido .= "\r\n\r\n$link\r\n\r\n";
		$contenido .= "SI NO ACTIVAS TU CUENTA NO PODRAS VER NADA!\r\n";
		$contenido .= "Guarda tu informacion en un lugar seguro!\r\n";

		$this->sendMail($email,$email,$tema,$contenido);
		
		return 1;
	}
	
	function sendMail($to_name,$to_email,$subject,$msg) {
		/***************************
		include_once("Mail.php");
		
		$recipients = $this->app['siteemail'];
		
		$headers["From"]    = $this->app['siteemail'];
		$headers["To"]      = $to_email;
		$headers["Subject"] = $subject;
		$headers["Content-type"] = "text/plain; charset=utf-8";
		
		$body = $msg;
		
		$params["host"] = $this->app['smtp_host'];
		$params["port"] = $this->app['smtp_port'];
		$params["auth"] = $this->app['smtp_auth'];
		$params["username"] = $this->app['smtp_username'];
		$params["password"] = $this->app['smtp_password'];
		
		// Create the mail object using the Mail::factory method
		$mail_object =& Mail::factory("smtp", $params);
		
		return $mail_object->send($recipients, $headers, $body);
		***************************
		$headers = "From: " . $this->app['siteemail'] . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
		$body = $msg;
		mail($to_email,$subject,$body,$headers);
	}
	
	*/
?>