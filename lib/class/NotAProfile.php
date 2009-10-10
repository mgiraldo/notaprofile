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
	 * Función constructora vacia
	 * @return No return
	 */
	function notAProfile(){
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
	public static function registrarUsuario($email, $clave, $reclave){
		//TODO
	}
	
	/**
	 * Función que valida un usuario en el sistema e inicia su sesión.
	 * @param $email
	 * @param $clave
	 * @return boolean, true o false en caso de que los datos sean correctos o no.
	 */
	public static function validarUsuario($email, $clave){
		//TODO
	}
	
	/**
	 * Función que verifica si un usuario representado con tu email existe 
	 * o no en el sistema.
	 * @param $email
	 * @return boolean, true o false en caso de existir o no en el sistema. 
	 */
	public static function existeUsuario($email){
		//TODO
	}
	
	/**
	 * Función que envia a un usuario determinado un email de confirmación para 
	 * poder validar su cuenta y asignar valor "activo" a dicho usuario.
	 * @param $email
	 * @return No return
	 */
	public static function enviarEmailValidacion($email){
		//TODO
	}
	
	/**
	 * Función que se encarga de activar a un usuario determinado dado su email en el sistema.
	 * @param $email
	 * @return No return
	 */
	public static function activarUsuario($email){
		//TODO 
	}
	
	/**
	 * Función que se encarga de cerrar la sesión de un usuario dado su email
	 * @param $email
	 * @return No return
	 */
	public static function cerrarSesion ($email){
		//TODO
	}

//----------------------------------------------------------------------------------------------
// Funciones relacionadas con la creación, reclamo y validación de llaves
//----------------------------------------------------------------------------------------------	

	//TODO Realizar el esqueleto VIEDA
	//Documentar y declarar funciones

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
	
}
?>