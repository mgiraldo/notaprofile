<?php 
require_once('config/config.php'); 
require_once('PPUpload.php');
require_once 'DAO.php';

/**
 * Clase principal del sistema
 * @author DISE3320 - 20092
 */
class NotAProfile{
	
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
		if(NotAProfile::existeUsuario($email)){
			return 4;
		}

		// Ingresa a la base de datos el nuevo usuario.
		$sql = "INSERT INTO usuario (id, email, clave, flag_activo, fecha_creado) VALUES (NULL, '$email', '".md5($clave)."', '0', NOW() )";
		if(DAO::doSQL($sql)){
			// Enviar email
			$id = DAO::lastId();
			NotAProfile::enviarEmailValidacion($email, $id);
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
		session_start();
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
	 * Función que cambia la clave de un usuario dado.
	 * @param $email Email del usuario
	 * @param $clave Clave que aigna el usuario a su cuenta
	 * @param $reclave Confirmación de la clave
	 * @param $token Token de reactivación
	 * @return  
	 *   1 - Alguno de los parametros se encuentra en blanco
	 *   2 - El campo del email no tiene el formato correcto
	 *   3 - Las contraseñas no coinciden
	 *   4 - El email ingresado no coincide con el código
	 *   5 - Problema ingresando datos a la base de datos
	 */
	public static function cambiarClave($email, $clave, $clave2, $param){
		
		$email = trim($email);
		$clave = trim($clave);
		$clave2 = trim($clave2);
		list($basura, $token) =split("-", $param);
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
		$user = NotAProfile::infoUsuario($email);
		if(!isset($user[0]['email'])||$user[0]['token_reactivacion']!=$token){
			return 4;
		}
		
		// Ingresa a la base de datos el nuevo usuario.
		$sql = sprintf("UPDATE usuario SET clave = '%s', token_reactivacion = NULL  WHERE email ='%s'",md5($clave),$email);
		return DAO::doSQL($sql)? 0:5;

	}
	
	/**
	 * Esta función se encarga de regresar un vector con toda la información de un usario
	 * identificado con una direccion de email que ingresa por parámetro
	 * @param unknown_type $email - Email de un usuario
	 * @return unknown_type - Vector con los datos de tabla usuario de BD
	 */
	public static function infoUsuario($email){
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
		$sql=sprintf("UPDATE usuario SET id_activacion = '%s' WHERE id = '%s'", $codigoUnico, $id);
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
	 * Esta función se encarga de regresar un booleano que indica si la llave puede ser vista o no por el usuario logeado.
	 */
	public static function puedeSerVista($llave)
	{
		if($llave['reclamador_id']==$_SESSION['userid']||$llave['creador_id']==$_SESSION['userid'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Función que envia a un usuario determinado un email con un código para cambiar
	 * su clave.
	 * @param $email
	 */
	public static function enviarEmailCambioClave($email){
		global $app;
		//Crea el código de activación usando como parametro el email y el tiempo el milisegundos 
		// con aumento de la entropia activado.
		$codigoUnico = md5(uniqid($email.mt_rand(), true));
		$usuario = NotAProfile::infoUsuario($email);
		if(!isset($usuario[0]['email']))
		{
			return false;
		}
		//ingresa a la base de datos el id relacionado al correo
		$sql=sprintf("UPDATE usuario SET token_reactivacion = '%s' WHERE id = '%s'", $codigoUnico, $usuario[0]['id']);
		DAO::doSQL($sql);
				
		// enviar correo con este codigo dentro de un link
		$link = $app['url'] ."forgotPassword.php?c=".md5($email)."-". $codigoUnico;
		
		List ($nombre, $empresaEmail) = split("@", $email);
		$msg = "Hello $nombre!, \r\n\r\n
		
		You have asked for this email to change your notaprofile password. To change it just click on the next link: \r\n\r\n
		
		$link \r\n\r\n
		
		 not_a_profile Team \r\n
		{$app['url']}\r\n
		
		
		";
		NotAProfile::sendMail($email, $email, 'Password Changing in Not_A_Profile!', $msg);
		return true;
	}
	
	
	/**
	 * Función que revisa si existe un email asociado a un token de cambio de clave.
	 * retorna el email en caso exitoso
	 * retorna -1 si hay error
	 */
	public static function existeCambioClave($param)
	{
		list($email, $token) =split("-", $param);
		$sql = sprintf("SELECT * FROM usuario WHERE token_reactivacion='%s'",$token);
		$usuario = DAO::doSQLAndReturn($sql);	
		if(md5($usuario[0]['email']) == $email)	{
			$emailUsuario = $usuario[0]['email'];
			return $emailUsuario;
		}
		return -1;
	}
	/**
	 * Función que se encarga de verificar si el código unico existe en la base de datos, en caso de que sí
	 * exista activa el usuario relacionado con el código y retorna el nombre del usuario activado, en caso de que no
	 * es reornado un mensaje de error. 
	 * @param $codigoActivacion
	 * @return 
	 * 	$nombreUsuario - si el código era valido y el usuario fue activado
	 * 	0 - en caso de que el código no coinsida con ningun registro en la BD
	 */
	public static function activarUsuario($codigoActivacion){
		//Verifica si el código si pertenece a algun registro en la BD
		$resultados=DAO::doSQLAndReturn("SELECT count(*) as Contador FROM usuario WHERE id_activacion='$codigoActivacion'");
	   	$existe = $resultados[0]["Contador"];
		

		$resp = 0;
		//Si existe realiza la activación y retorna el nombre del usuario
		if($existe == 1){
			//Activa al usuario en la BD
			$sql=sprintf("UPDATE usuario SET flag_activo = '1' WHERE id_activacion = '%s'",$codigoActivacion);
			DAO::doSQL($sql);
			
			//Consulta el email del usuario activado
			$sql=sprintf("SELECT email FROM usuario WHERE id_activacion='%s'",$codigoActivacion);
			$email = DAO::doSQLAndReturn($sql);
			List ($nombreUsuario, $empresaEmail) = split("@", $email[0]["email"]);
			$resp = $nombreUsuario;

		}
		
		return $resp;		
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
	}

	
	/**
	 * Función que revisa si el usuario está logeado o no 
	 * @return true - Se encuentr un userid en la sesion
	 *         false - de lo contrario
	 */
	public static function estaLogeado(){
		return isset($_SESSION['userid'])? true: false; 
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
	 public static function crearLlave($lat, $long, $texto, $foto){
	 	global $app;
		$conn = DAO::getConn();
		$codigo = NotAProfile::elegirCodigoUnico();
		//El id del creador es siempre 1 para probar la creación de llaves.
		$creador_id= $_SESSION['userid'];
		if(!isset($creador_id)){echo("you need to be logged in");exit;}	
			$fecha = date("c");
			$sql = sprintf("INSERT INTO llave (txt,latitud,longitud,codigo,creador_id,fecha_creado,foto) VALUES ('%s',%s,%s,'%s','%s','%s','%s')",
						mysql_real_escape_string($texto, $conn),
						mysql_real_escape_string($lat, $conn),
						mysql_real_escape_string($long, $conn),
						mysql_real_escape_string($codigo, $conn),
						mysql_real_escape_string($creador_id, $conn),
						mysql_real_escape_string($fecha, $conn),
						mysql_real_escape_string($foto, $conn)
						);
			$exito = DAO::doSQL($sql);
			if($exito!=1)
			{
				$codigo="error";
				return $codigo;
				exit;
			}
	
			$url = $app['url']."key/".$codigo;
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
		while($noesunico==1){
			$noesunico=0;
			for ($index = 0; $index < count($cods) && $noesunico==0; $index++) {
				if($filename==$cods[$index]['codigo']){
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
			if(!isset($idUuario))
			{
				$idUuario=-1;
			}
			$fecha = date("c");
			$sql=sprintf("UPDATE llave SET reclamador_id = '%s',fecha_reclamado = '%s' WHERE codigo = '%s'",$idUuario,$fecha,$codigoLlave);
			DAO::doSQL($sql);
			return $llave;
		}
	}
	/**
	 * Este metodo se encarga de cambiar al usuario que reclamó una llave, se utiliza para el login despues de haber visto una llave.
	 * @param unknown_type $idLlave
	 * @param unknown_type $idUuario
	 * @return unknown_type
	 */
	public static function rereclamarLlave($codigoLlave, $idUuario){
		$sql=sprintf("SELECT * FROM llave WHERE codigo='%s'",$codigoLlave);
		$llave = DAO::doSQLAndReturn($sql);
		if(!isset($llave[0]['id']))
		{
			return "Error, la llave no existe.";
			exit;
		}
		else
		{
			if(!isset($idUuario)){
				$idUuario=-1;
			}
			$fecha = date("c");
			$sql=sprintf("UPDATE llave SET reclamador_id = '%s',fecha_reclamado = '%s' WHERE codigo = '%s'",$idUuario,$fecha,$codigoLlave);
			DAO::doSQL($sql);
			return $llave;
		}
	}
	
	/**
	 * Este metodo se encarga de devolver la llave despues de que fue reclamada.
	 * @return unknown_type
	 */
	public static function darLlave($codigoLlave){
		$sql=sprintf("SELECT * FROM llave WHERE codigo='%s'",$codigoLlave);
		return  DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Este metodo se encarga de marcar una llave como aceptada, despues de haber sido reclamada
	 * @param unknown_type $idLlave
	 */
	public static function aceptarLlave($codigoLlave){
			$sql=sprintf("UPDATE llave SET flag_aceptado = 1 WHERE codigo = '%s'",$codigoLlave);
			return DAO::doSQL($sql);
	}
	
	/**
	 * Este metodo se encarga de marcar una llave como aceptada, despues de haber sido reclamada
	 * @param unknown_type $idLlave
	 */
	public static function rechazarLlave($codigoLlave){
			$sql=sprintf("UPDATE llave SET flag_aceptado = -1 WHERE codigo = '%s'",$codigoLlave);
			return DAO::doSQL($sql);
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
	 * Función que se encarga de devolver las llaves creadas por el usuario logeado que ya han sido reclamadas.
	 */
	public static function darLlavesCreadasReclamadas(){
		$conn = DAO::getConn();
		$sql = sprintf("SELECT * FROM llave WHERE reclamador_id = %s OR (creador_id = %s AND reclamador_id > 0)",
																		 mysql_real_escape_string($_SESSION['userid'], $conn),
																		 mysql_real_escape_string($_SESSION['userid'], $conn));
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	/**
	 * Función que se encarga de devolver las llaves creadas por el usuario logeado que no han sido reclamadas.
	 */
	public static function darLlavesCreadasNoReclamadas(){
		global $app;
		$sql = sprintf("SELECT * FROM llave WHERE creador_id = %s AND reclamador_id IS NULL AND DATEDIFF( NOW( ) , fecha_creado) < %s", $_SESSION['userid'], $app['dias_key_caduca']);
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Función que se encarga de devolver las llaves creadas por el usuario logeado que no han sido reclamadas.
	 */
	public static function darLlavesCreadasVencidas(){
		global $app;
		$sql = sprintf("SELECT * FROM llave WHERE creador_id = %s AND reclamador_id IS NULL AND DATEDIFF(NOW( ) , fecha_creado )> %s",$_SESSION['userid'], $app['dias_key_caduca']	);
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	
	
	/**
	 * Función que se encarga de devolver las llaves que han sido reclamadas por el usuario logeado.
	 */
	public static function darLlavesReclamadas(){
		$sql = sprintf("SELECT * FROM llave WHERE reclamador_id = %s",$_SESSION['userid']);
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Este método retorna todas las llaves que se encuentran disponibles (No han sido reclamadas)
	 * @return unknown_type
	 */
	public static function darLlavesDisponibles(){
		$sql = "SELECT * FROM llave WHERE reclamador_id IS NULL";
		$llaves = DAO::doSQLAndReturn($sql);
		return $llaves;
	}
	
	
	/**
	 * Este método retorna el numero de llaves que se encuentran disponibles (No han sido reclamadas)
	 * @return unknown_type - Numero de llaves disponibles
	 */
	public static function contarLlavesDisponibles(){
		$sql = "SELECT count(*) AS cont FROM llave WHERE reclamador_id IS NULL";
		$llaves = DAO::doSQLAndReturn($sql);
		return $llaves[0]['cont'];
	}
	
	/**
	 * Este método retorna todas las llaves que han sido reclamadas por mis contactos o por mi
	 */
	public static function darLlavesReclamadas2(){
		$contactos  = NotAProfile::darContactos();
		$arregloLlaves = array();
		for ($index = 0; $index < count($contactos); $index++) {
			if(isset($contactos[$index]['amigo_idc1']))
			{
				$idpersona =$contactos[$index]['amigo_idc1'];
			}
			else
			{
				$idpersona =$contactos[$index]['amigo_idc2'];
			}
			$sql = sprintf("SELECT * FROM llave WHERE creador_id = '%s' AND reclamador_id = '%s' UNION SELECT * FROM llave WHERE reclamador_id = '%s' AND creador_id = '%s'",$idpersona, $_SESSION['userid'],$idpersona, $_SESSION['userid']);
			$llaves = DAO::doSQLAndReturn($sql);
			$arregloLlaves = array_merge($arregloLlaves, $llaves);
		}
		return $arregloLlaves;
	}
	
	/**
	 * Este método retorna todos los contactos de un usuario dado.
	 */
	public static function darContactos(){
		/**
		//Consulta que devuelve una tabla con la información de cada una de las llaves reclamadas al usuario logeado y la información del usuario reclamador.
		$contactossql1 = sprintf("SELECT usuario.id, email  FROM usuario LEFT JOIN llave ON usuario.id = llave.reclamador_id WHERE flag_aceptado = 1 AND creador_id = '%s' GROUP BY email" , $_SESSION['userid']);
		$contactos1 = DAO::doSQLAndReturn($contactossql1);
		//Consulta que devuelve una tabla con cada la información de cada una de las llaves reclamadas por usuario logeado y la información del usuario creador.
		$contactossql2 = sprintf("SELECT usuario.id, email FROM usuario LEFT JOIN llave ON usuario.id = llave.creador_id WHERE flag_aceptado = 1 AND reclamador_id = '%s' GROUP BY email", $_SESSION['userid']);
		$contactos2 = DAO::doSQLAndReturn($contactossql2);
		//Concatenamos ambos arreglos
		$contactos = array_merge($contactos1, $contactos2);
		**/
		
		//Consulta que devuelve una tabla con las personas que han creado llaves reclamadas por el usuario logeado.
		$contactossql1 = sprintf("(SELECT reclamados_el, reclamados_yo, amigo_idc1, amigo_idc2  FROM  (SELECT t1.reclamador_id as amigo_idc1, COUNT( t1.id ) AS reclamados_el
		FROM llave AS t1
		WHERE t1.reclamador_id >0
		AND t1.creador_id = '%s'
		GROUP BY t1.reclamador_id) AS c1  LEFT JOIN (SELECT t1.creador_id as amigo_idc2, COUNT( t1.id ) AS reclamados_yo
		FROM llave AS t1
		WHERE t1.reclamador_id ='%s'
		AND t1.creador_id >0
		GROUP BY t1.creador_id) AS c2 ON c1.amigo_idc1 = c2.amigo_idc2) 
		UNION
		(SELECT reclamados_el, reclamados_yo, amigo_idc1, amigo_idc2  FROM  (SELECT t1.reclamador_id as amigo_idc1, COUNT( t1.id ) AS reclamados_el
		FROM llave AS t1
		WHERE t1.reclamador_id >0
		AND t1.creador_id = '%s'
		GROUP BY t1.reclamador_id) AS c1  RIGHT JOIN (SELECT t1.creador_id as amigo_idc2, COUNT( t1.id ) AS reclamados_yo
		FROM llave AS t1
		WHERE t1.reclamador_id ='%s'
		AND t1.creador_id >0
		GROUP BY t1.creador_id) AS c2 ON c1.amigo_idc1 = c2.amigo_idc2) 
		" , $_SESSION['userid'], $_SESSION['userid'], $_SESSION['userid'], $_SESSION['userid']);
		$contactos1 = DAO::doSQLAndReturn($contactossql1);
		return $contactos1;
	}
	
	/*
	 * Función que se encarga de subir la imagen de la llave.
	 * @param $field: nombre del campo post: 
	 */
	public static function subirFoto ($field) {
		global $app;
		$filename = PPUpload::checkAndUpload ($field,$app['photoroot'],"",0);
		$e = PPUpload::resizeViral($filename);
		if ($e==-1) {
			return "error_notimage";
		} else if ($e==-2) {
			return "error_nofile";
		} else {
			return $e;
		}
		return $e;
	}
	
	/*
	 * 
	 * Función dque devuelve una imagen aleatoria de la relación entre dos personas
	 */
	public static function darThumbnailRelacion($idamigo)
	{
		global $app;
		$consulta = sprintf("SELECT * FROM llave WHERE reclamador_id = %s AND creador_id = %s UNION SELECT * FROM llave WHERE reclamador_id = %s AND creador_id = %s", $_SESSION['userid'],$idamigo,$idamigo,$_SESSION['userid']);
		$llaves = DAO::doSQLAndReturn($consulta);
		$llave = rand(0,count($llaves)-1);
		return $llaves[$llave]['foto'];
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
		global $app;
		$headers = "From: " . $app['siteemail'] . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
		$body = $msg;
		mail($to_email,$subject,$body,$headers);
	}
	
	/*
	
	FUNCIONES PARA CREAR LLAVES DESDE EL MAIL
	
	*/
	
	/*
	 * Función que se encarga de subir la imagen de la llave enviada desde email.
	 * @param $str: string binario con el archivo: 
	 */
	public static function subirFotoEmail ($str) {
		global $app;
		$filename = date('YmdHis') . rand(1000,9999) . ".xxx";
		$full = $app['siteroot'] . $app['photoroot'] . $filename;
		$data = base64_decode($str);
		$im = imagecreatefromstring($data);
		if ($im != false) {
			imagepng($im, $full);
		}
		/**/
		$e = PPUpload::resizeViral($filename);
		if ($e==-1) {
			return "error_notimage";
		} else if ($e==-2) {
			return "error_nofile";
		} else {
			return $e;
		}
		return $e;
		/**/
	}

	public static function readMail ($host,$login,$password,$must_delete=false) {
		$r = array();
		// Open pop mailbox
		if (!$mbox = imap_open ($host, $login, $password)) {
			die ('Cannot connect/check pop mail! Exiting');
		}
		
		if ($hdr = imap_check($mbox)) {
			$msgCount = $hdr->Nmsgs;
		} else {
			echo "Failed to get mail";
			exit;
		}
		
		$MN=$msgCount;
		
		for ($X = 1; $X <= $MN; $X++) {
			$r[$X] = array();
			$r[$X]["from"] = "";
			$r[$X]["text"] = "";
			$r[$X]["attachment"] = array();
			$overview = imap_fetch_overview($mbox, $X);
			$r[$X]["from"] = $overview[0]->from;
			$headers = imap_headerinfo($mbox, $X);
			$r[$X]["subject"] = $headers->subject;
			$struct = imap_fetchstructure($mbox, $X);
			$parts = NotAProfile::create_part_array($struct);
			foreach ($parts as $part) {
				if ($part["part_object"]->type==0) {
					// es texto... meter en el string
					$r[$X]["text"] .= imap_fetchbody($mbox,$X,$part["part_number"]);
				} else if ($part["part_object"]->type==5) {
					$r[$X]["attachment"]["filename"] = $part["part_object"]->dparameters[0]->value;
					$r[$X]["attachment"]["string"] = imap_fetchbody($mbox,$X,$part["part_number"]);
				}
			}
			if ($must_delete) {
				imap_delete($mbox, $X);
			}
		}
		
		if ($must_delete) {
			imap_expunge($mbox);
		}
		imap_close($mbox);
		return $r;
	}
	
	 public static function crearLlaveEmail($lat, $long, $texto, $foto, $email){
	 	global $app;
		$conn = DAO::getConn();
		$codigo = NotAProfile::elegirCodigoUnico();
		// buscar el mail
		$sql = sprintf("SELECT id FROM usuario WHERE email = '%s'",
						mysql_real_escape_string($email, $conn)
						);
		$q = DAO::doSQLAndReturn($sql);
		if (count($q)>0) {
			$creador_id= $q[0]['id'];
			$fecha = date("c");
			$sql = sprintf("INSERT INTO llave (txt,latitud,longitud,codigo,creador_id,fecha_creado,foto) VALUES ('%s',%s,%s,'%s',%s,'%s','%s')",
						mysql_real_escape_string($texto, $conn),
						mysql_real_escape_string($lat, $conn),
						mysql_real_escape_string($long, $conn),
						mysql_real_escape_string($codigo, $conn),
						mysql_real_escape_string($creador_id, $conn),
						mysql_real_escape_string($fecha, $conn),
						mysql_real_escape_string($foto, $conn)
						);
			DAO::doSQL($sql);
			$url = $app['url']."key/".$codigo;
			return $url;
		} else {
			return false;
		}
	}
	
	public static function mailKeyCode () {
		global $app;
		// sacar esto al config luego
		$host="{s9117.gridserver.com:110/pop3}"; // pop3host
		$login="upload@notaprofile.com"; //pop3 login
		$password="notaprof1le"; //pop3 password
		// fin sacar config
		
		require_once('classTextile.php');		
		$textile = new Textile();

		$mails = NotAProfile::readMail($host,$login,$password,true);
		
		// procesa todos los mails que haya
		if (count($mails)>0) {
			for ($i=1;$i<=count($mails);$i++) {
				$data = $mails[$i];
				if ($data["from"]!="") {
					$filename = $data["attachment"]["filename"];
					$content = $data["attachment"]["string"];
					if ($filename!="") {
						$foto = NotAProfile::subirFotoEmail($content);
					} else {
						$foto = "";
					}
					$coords = explode(",",$data["subject"]);
					if (count($coords)==2) {
						$lat = $coords[0] ? $coords[0] : NULL;
						$lng = $coords[1] ? $coords[1] : NULL;
					}
					$textile = new Textile();
					$texto = $textile->TextileThis($data["text"]);
					// se asume que el mail es de la forma: Nombre <email@sitio.com>
					$tmp = explode("<",$data["from"]);
					$stripmail = $tmp[1];
					$stripmail = substr($stripmail,0,strlen($stripmail)-1);
					$url = NotAProfile::crearLlaveEmail($lat,$lng,$texto,$foto,$stripmail); // luego sacar lat/long desde el EXIF
					if ($url!=false) {
						$txt = $url."\r\n\r\n";
						NotAProfile::sendMail("",$data["from"],"your key in not_a_profile",$txt);
					}
				}
			}
		}
	}
	
	public static function create_part_array($struct) {
		if (sizeof($struct->parts) > 0) {    // There some sub parts
			foreach ($struct->parts as $count => $part) {
				NotAProfile::add_part_to_array($part, ($count+1), $part_array);
			}
		}else{    // Email does not have a seperate mime attachment for text
			$part_array[] = array('part_number' => '1', 'part_object' => $struct);
		}
	   return $part_array;
	}
	
	// Sub function for create_part_array(). Only called by create_part_array() and itself.
	public static function add_part_to_array($obj, $partno, & $part_array) {
		$part_array[] = array('part_number' => $partno, 'part_object' => $obj);
		if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
			//print_r($obj);
			if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
				foreach ($obj->parts as $count => $part) {
					// Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
					if (sizeof($part->parts) > 0) {
						foreach ($part->parts as $count2 => $part2) {
							NotAProfile::add_part_to_array($part2, $partno.".".($count2+1), $part_array);
						}
					}else{    // Attached email does not have a seperate mime attachment for text
						$part_array[] = array('part_number' => $partno.'.'.($count+1), 'part_object' => $obj);
					}
				}
			}else{    // Not sure if this is possible
				$part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
			}
		}else{    // If there are more sub-parts, expand them out.
			if (sizeof($obj->parts) > 0) {
				foreach ($obj->parts as $count => $p) {
					NotAProfile::add_part_to_array($p, $partno.".".($count+1), $part_array);
				}
			}
		}
	}
	
	
	/**
	 * FUNCIONES DE COMPATIBILIDAD
	 */
	public static function youLike($id_otro){
		$id_otro = htmlentities($id_otro);
		if(!is_numeric($id_otro)){
			return "-";
		}
		
		$id_mio = $_SESSION['userid'];
		// Llaves que el reclamo y a mi me gustaron
		$sql = "SELECT COUNT(*) AS cuantas FROM llave WHERE creador_id = $id_otro AND reclamador_id = $id_mio AND flag_aceptado = 1";
		$resp = DAO::doSQLAndReturn($sql);
		return $resp[0]['cuantas'];	
	}
	
	public static function youDislike($id_otro){
		$id_otro = htmlentities($id_otro);
		if(!is_numeric($id_otro)){
			return "-";
		}
		
		$id_mio = $_SESSION['userid'];
		// Llaves que el reclamo y a mi no me gustaron
		$sql = "SELECT COUNT(*) AS cuantas FROM llave WHERE creador_id = $id_otro AND reclamador_id = $id_mio AND flag_aceptado = -1";
		$resp = DAO::doSQLAndReturn($sql);
		return $resp[0]['cuantas'];	
	}
	
	public static function heLikes($id_otro){
		$id_otro = htmlentities($id_otro);
		if(!is_numeric($id_otro)){
			return "-";
		}
		
		$id_mio = $_SESSION['userid'];
		// Llaves que yo reclame y a el le gustaron
		$sql = "SELECT COUNT(*) AS cuantas FROM llave WHERE creador_id = $id_mio AND reclamador_id = $id_otro AND flag_aceptado = 1";
		$resp = DAO::doSQLAndReturn($sql);
		return $resp[0]['cuantas'];	
	}
	
	public static function heDislikes($id_otro){
		$id_otro = htmlentities($id_otro);
		if(!is_numeric($id_otro)){
			return "-";
		}
		
		$id_mio = $_SESSION['userid'];
		// Llaves que el reclamo y a mi me gustaron
		$sql = "SELECT COUNT(*) AS cuantas FROM llave WHERE creador_id = $id_mio AND reclamador_id = $id_otro AND flag_aceptado = -1";
		$resp = DAO::doSQLAndReturn($sql);
		return $resp[0]['cuantas'];	
	}
	
	public static function compatibilidad($id_otro){
		
		$id_otro = htmlentities($id_otro);
		if(!is_numeric($id_otro)){
			return "-";
		}
		
		$id_mio = $_SESSION['userid'];
		
		/* *  Calculo de compatiblidad:
		 *  x = 2*(youLike)-1(youDislike)+3(heLikes)-2(heDisplikes)  */
		$ilike = NotAProfile::youLike($id_otro);
		$idislike = NotAProfile::youDislike($id_otro);
		$heLikes = NotAProfile::heLikes($id_otro);
		$heDislike = NotAProfile::heDislikes($id_otro);
		
		
		$sql = "SELECT COUNT(*) AS cuantas FROM llave WHERE (creador_id = $id_otro AND reclamador_id = $id_mio) OR (creador_id = $id_mio AND reclamador_id = $id_otro)";
		$resp = DAO::doSQLAndReturn($sql);
		$totalLlaves = $resp[0]['cuantas'];
		$valor = 3*$ilike-$idislike+3*$heLikes-$heDislike;
		$num = $valor==0? 0: $totalLlaves/(3*$ilike-$idislike+3*$heLikes-$heDislike)*100;
	
		return number_format($num, 1)."%";
	}
	
	
	public static function darLlavesRelacion($id_otro){
		
		$id_otro = htmlentities($id_otro);
		if(!is_numeric($id_otro)){
			return array();
		}
		
		$id_mio = $_SESSION['userid'];
		$sql = "SELECT * FROM llave WHERE (creador_id = $id_otro AND reclamador_id = $id_mio) OR (creador_id = $id_mio AND reclamador_id = $id_otro)";
		return DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * ELIMINAR LLAVE
	 */
	public static function eliminarLlave($codigo){
		$codigo = htmlentities($codigo);
		$sql = "DELETE FROM llave WHERE codigo = '$codigo'";
		DAO::doSQL($sql); 
	}
	
	
	
	
}


?>