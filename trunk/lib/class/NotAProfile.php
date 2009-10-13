<?php

//Clases necesarias
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
	function notAProfile(){
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
	public static function registrarUsuario($email, $clave, $reclave){
		//TODO
	}
	
	/**
	 * Funci�n que valida un usuario en el sistema e inicia su sesi�n.
	 * @param $email
	 * @param $clave
	 * @return boolean, true o false en caso de que los datos sean correctos o no.
	 */
	public static function validarUsuario($email, $clave){
		//TODO
	}
	
	/**
	 * Funci�n que verifica si un usuario representado con tu email existe 
	 * o no en el sistema.
	 * @param $email
	 * @return boolean, true o false en caso de existir o no en el sistema. 
	 */
	public static function existeUsuario($email){
		//TODO
	}
	
	/**
	 * Funci�n que envia a un usuario determinado un email de confirmaci�n para 
	 * poder validar su cuenta y asignar valor "activo" a dicho usuario.
	 * @param $email
	 * @return No return
	 */
	public static function enviarEmailValidacion($email){
		//TODO
	}
	
	/**
	 * Funci�n que se encarga de activar a un usuario determinado dado su email en el sistema.
	 * @param $email
	 * @return No return
	 */
	public static function activarUsuario($email){
		//TODO 
	}
	
	/**
	 * Funci�n que se encarga de cerrar la sesi�n de un usuario dado su email
	 * @param $email
	 * @return No return
	 */
	public static function cerrarSesion ($email){
		//TODO
	}

//----------------------------------------------------------------------------------------------
// Funciones relacionadas con la creaci�n, reclamo y validaci�n de llaves
//----------------------------------------------------------------------------------------------	

	//TODO Realizar el esqueleto VIEDA
	//Documentar y declarar funciones
	
	//Creaci�n provisional de esta funci�n para la prueba del mapa.
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
	
	/** Funci�n que se encarga de crear una llave dados 3 par�metros b�sicos, latitud longitud y texto,
	 *  Esta funci�n es provisional para las pruebas.
	 *  devuelve el c�digo de la llave
	 *  @param $lat
	 *  @param $long
	 *  @param $texto
	 *  @return codigo, cadena de caracteres asociada a la llave. 
	 *  error en caso de no haber sido exitoso el proceso.
	 */
	public static function crearLlave($lat,$long,$texto){
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
}
?>