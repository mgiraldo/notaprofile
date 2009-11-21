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
	 * Funci�n que agrega un nuevo usuario al sistema.
	 * @param $email Email del nuevo usuario
	 * @param $clave Clave que aigna el usuario a su cuenta
	 * @param $reclave Confirmaci�n de la clave
	 * @return  
	 *   1 - Alguno de los parametros se encuentra en blanco
	 *   2 - El campo del email no tiene el formato correcto
	 *   3 - Las contrase�as no coinciden
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
		
		// Verfica que las dos contrase�as sean iguales
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
			$id = DAO::lastId();
			NotAProfile::enviarEmailValidacion($email, $id);
		}else{
			return 5;
		}
	}
	
	/**
	 * Funci�n que hace login del usuario
	 * @param unknown_type $email
	 * @param unknown_type $clave
	 * 
	 * Errores:
	 *   1 - En email o la contrase�a se encuentran en blanco
	 *   2 - El campo del email no tiene el formato correcto
	 *   3 - El email ingresado no existe o la contrase�a no coincide
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

		// Crea la sesi�n con las variables username y id
		$usuario = NotAProfile::infoUsuario($email);
		$_SESSION['username'] = $usuario[0]['email'];
		$_SESSION['userid']   = $usuario[0]['id'];
		
		return 0;
	}

	/**
	 * Esta funci�n se encarga de validar que exista un usuario dentro de la base de datos
	 * con el email ingresado por par�metro. Si este registro existe, valida que la clave
	 * asociada conicida con la clave ingresada por par�metro
	 * @param unknown_type $email  - Email de un usuario
	 * @param unknown_type $clave  - Constrase�a asociada al email 
	 * @return unknown_type - True si el usuario con $email existe y la coincide la clave,
	 *                        false de lo contrario
	 */
	public static function validarUsuario($email, $clave)
	{
		$sql = "SELECT clave FROM usuario WHERE email='$email'";
		$usuario = DAO::doSQLAndReturn($sql);
		if(count($usuario)==1)
			// Existe un �nico registro asociado al email
			return $usuario[0]['clave']==$clave?true:false;
		else 
			// No hay un registro asosciado a dicho email
			return false;
	}
	
	
/**
	 * Funci�n que cambia la clave de un usuario dado.
	 * @param $email Email del usuario
	 * @param $clave Clave que aigna el usuario a su cuenta
	 * @param $reclave Confirmaci�n de la clave
	 * @param $token Token de reactivaci�n
	 * @return  
	 *   1 - Alguno de los parametros se encuentra en blanco
	 *   2 - El campo del email no tiene el formato correcto
	 *   3 - Las contrase�as no coinciden
	 *   4 - El email ingresado no coincide con el c�digo
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
		
		// Verfica que las dos contrase�as sean iguales
		if($clave2!=$clave){return 3;}
		
		//Agrega caracteres de control XSS
		$email = strip_tags(addslashes(htmlspecialchars(htmlentities($email))));
		$clave = strip_tags(addslashes(htmlspecialchars(htmlentities($clave))));
		
		// Verifica que no exista un usuario con ese email registrado
		
		
		// verificar que el email no exista en la bd (llamar metodo)
		$user = NotAProfile::infoUsuario($email);
		if(!isset($user[0]['email'])||$user[0]['token_reactivacion']!=$token)
		{
			return 4;
		}
		// Ingresa a la base de datos el nuevo usuario.
		$sql = sprintf("UPDATE usuario SET clave = '%s' WHERE email ='%s'",md5($clave),$email);
		if(DAO::doSQL($sql)){
	
		}else{
			return 5;
		}
	}
	/**
	 * Esta funci�n se encarga de regresar un vector con toda la informaci�n de un usario
	 * identificado con una direccion de email que ingresa por par�metro
	 * @param unknown_type $email - Email de un usuario
	 * @return unknown_type - Vector con los datos de tabla usuario de BD
	 */
	public static function infoUsuario($email)
	{
		$sql = "SELECT * FROM usuario WHERE email= '$email'";
		return DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Funci�n que verifica si un usuario representado con su email existe 
	 * o no en el sistema.
	 * @param $email
	 * @return boolean, true o false en caso de existir o no en el sistema. 
	 */
	 public static function existeUsuario($email){
		
	   $resultados=DAO::doSQLAndReturn("SELECT count(*) as Contador FROM usuario WHERE email='$email'");
	   return $resultados[0]["Contador"]==0?false:true;
	   
	}
	
	/**
	 * Funci�n que envia a un usuario determinado un email de confirmaci�n para 
	 * poder validar su cuenta y asignar valor "activo" a dicho usuario.
	 * @param $email
	 * @return No return
	 */
	public static function enviarEmailValidacion($email, $id){
		global $app;
		//Crea el c�digo de activaci�n usando como parametro el email y el tiempo el milisegundos 
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
		NotAProfile::sendMail($email, $email, 'Activaci�n en Not_A_Profile!', $msg);
	}
	
	/**
	 * Funci�n que envia a un usuario determinado un email con un c�digo para cambiar
	 * su clave.
	 * @param $email
	 */
	public static function enviarEmailCambioClave($email){
		global $app;
		//Crea el c�digo de activaci�n usando como parametro el email y el tiempo el milisegundos 
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
	 * Funci�n que revisa si existe un email asociado a un token de cambio de clave.
	 * retorna el email en caso exitoso
	 * retorna -1 si hay error
	 */
	public static function existeCambioClave($param)
	{
		list($email, $token) =split("-", $param);
		$sql = sprintf("SELECT * FROM usuario WHERE token_reactivacion='%s'",$token);
		$usuario = DAO::doSQLAndReturn($sql);	
		if(md5($usuario[0]['email']) == $email)
		{
			$emailUsuario = $usuario[0]['email'];
			return $emailUsuario;
		}
		return "error";
	}
	/**
	 * Funci�n que se encarga de verificar si el c�digo unico existe en la base de datos, en caso de que s�
	 * exista activa el usuario relacionado con el c�digo y retorna el nombre del usuario activado, en caso de que no
	 * es reornado un mensaje de error. 
	 * @param $codigoActivacion
	 * @return 
	 * 	$nombreUsuario - si el c�digo era valido y el usuario fue activado
	 * 	0 - en caso de que el c�digo no coinsida con ningun registro en la BD
	 */
	public static function activarUsuario($codigoActivacion){
		//Verifica si el c�digo si pertenece a algun registro en la BD
		$resultados=DAO::doSQLAndReturn("SELECT count(*) as Contador FROM usuario WHERE id_activacion='$codigoActivacion'");
	   	$existe = $resultados[0]["Contador"];
		

		$resp = 0;
		//Si existe realiza la activaci�n y retorna el nombre del usuario
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
	 * Funci�n que se encarga de cerrar la sesi�n de un usuario dado su email
	 * @param $email
	 * @return No return
	 */
	public static function cerrarSesion (){
		//Elimina la seci�n
		session_start(); 
		$_SESSION = array(); 
		session_destroy();

		
		//Redirecciona a la pagina principal
		//header ("Location: index.php");  
	}

	
	/**
	 * Funci�n que revisa si el usuario est� logeado
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
	 public static function crearLlave($lat, $long, $texto, $foto){
	 	global $app;
		$codigo = NotAProfile::elegirCodigoUnico();
		//El id del creador es siempre 1 para probar la creaci�n de llaves.
		$creador_id= $_SESSION['userid'];
		if(!isset($creador_id)){echo("you need to be logged in");exit;}	
			$fecha = date("c");
			$sql = sprintf("INSERT INTO llave (txt,latitud,longitud,codigo,creador_id,fecha_creado,foto) VALUES ('%s','%s','%s','%s','%s','%s','%s')",
						$texto,
						$lat,
						$long,
						$codigo,
						$creador_id,
						$fecha,
						$foto
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
	 * Elige un c�digo �nico para la llave
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
	 * Este metodo se encarga de cambiar al usuario que reclam� una llave, se utiliza para el login despues de haber visto una llave.
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
	 * Este metodo se encarga de devolver la llave despues de que fue reclamada.
	 * @return unknown_type
	 */
	public static function darLlave($codigoLlave){
		$sql=sprintf("SELECT * FROM llave WHERE codigo='%s'",$codigoLlave);
		$llave = DAO::doSQLAndReturn($sql);
		return $llave;
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
	 * Esta metodo se encarga de realizar una validaci�n de llave
	 * @param unknown_type $idLlave
	 * @return unknown_type
	 */
	public static function validarLlave($idLlave){
		// TODO
	}
	
/**
	 * Funci�n que se encarga de devolver las llaves creadas por el usuario logeado que ya han sido reclamadas.
	 */
	public static function darLlavesCreadasReclamadas(){
		$sql = sprintf("SELECT * FROM Llave WHERE creador_id = %s AND reclamador_id IS NOT NULL",$_SESSION['userid']);
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	/**
	 * Funci�n que se encarga de devolver las llaves creadas por el usuario logeado que no han sido reclamadas.
	 */
	public static function darLlavesCreadasNoReclamadas(){
		//TODO filtrar las llaves y dejar solo las que no se han vencido.
		$sql = sprintf("SELECT * FROM Llave WHERE creador_id = %s AND reclamador_id IS NULL",$_SESSION['userid']);
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Funci�n que se encarga de devolver las llaves creadas por el usuario logeado que no han sido reclamadas.
	 */
	public static function darLlavesCreadasVencidas(){
		//TODO filtrar las llaves y dejar solo las vencidas.
		$sql = sprintf("SELECT * FROM Llave WHERE creador_id = '%s' AND reclamador_id IS NULL",$_SESSION['userid']);
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	
	
	/**
	 * Funci�n que se encarga de devolver las llaves que han sido reclamadas por el usuario logeado.
	 */
	public static function darLlavesReclamadas(){
		$sql = sprintf("SELECT * FROM Llave WHERE reclamador_id = '%s'",$_SESSION['userid']);
		return $llaves = DAO::doSQLAndReturn($sql);
	}
	
	/**
	 * Este m�todo retorna todas las llaves que se encuentran disponibles (No han sido reclamadas)
	 * @return unknown_type
	 */
	public static function darLlavesDisponibles(){
		$sql = "SELECT * FROM llave WHERE reclamador_id IS NULL";
		$llaves = DAO::doSQLAndReturn($sql);
		return $llaves;
	}
	
	/**
	 * Este m�todo retorna todas las llaves que se encuentran disponibles y que han sido publicadas por contactos
	 */
	public static function darLlavesDisponiblesContactos(){
		$contactos  = NotAProfile::darContactos();
		$arregloLlaves = array();
		for ($index = 0; $index < count($contactos); $index++) {
			$sql = sprintf("SELECT * FROM llave WHERE creador_id = '%s' AND reclamador_id IS NULL",$contactos[$index]['id']);
			$llaves = DAO::doSQLAndReturn($sql);
			$arregloLlaves = array_merge($arregloLlaves, $llaves);
		}
		return $arregloLlaves;
	}
	
	/**
	 * Este m�todo retorna todos los contactos de un usuario dado.
	 */
	public static function darContactos(){
		/**
		//Consulta que devuelve una tabla con la informaci�n de cada una de las llaves reclamadas al usuario logeado y la informaci�n del usuario reclamador.
		$contactossql1 = sprintf("SELECT usuario.id, email  FROM usuario LEFT JOIN llave ON usuario.id = llave.reclamador_id WHERE flag_aceptado = 1 AND creador_id = '%s' GROUP BY email" , $_SESSION['userid']);
		$contactos1 = DAO::doSQLAndReturn($contactossql1);
		//Consulta que devuelve una tabla con cada la informaci�n de cada una de las llaves reclamadas por usuario logeado y la informaci�n del usuario creador.
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
	 * Funci�n que se encarga de subir la imagen de la llave.
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