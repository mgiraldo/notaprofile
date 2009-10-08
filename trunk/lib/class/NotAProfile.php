<?php
require_once 'DAO.php';

/**
 * Funci�n que procesa el inicio (login/registro) al sistema. 
 * En caso de encontrar un email, clave y reclave se asume que se esta registrando.
 * En caso de encontrar unicamente email y clave se asume que se esta logeando.
 * @param $email
 * @param $clave
 * @param $reclave
 * @return unknown_type
 */
function procesarInicio($email, $clave, $reclave){
	
}

/**
 * Funci�n que agrega un nuevo usuario al sistema.
 * @param $email Email del nuevo usuario
 * @param $clave Clave que aigna el usuario a su cuenta
 * @param $reclave Confirmaci�n de la clave
 * @return no return
 */
function registrarUsuario($email, $clave, $reclave){
	

}

/**
 * Funci�n que valida un usuario en el sistema e inicia su sesi�n.
 * @param $email
 * @param $clave
 * @return boolean, true o false en caso de que los datos sean correctos o no.
 */
function validarUsuario($email, $clave){
	
}

/**
 * Funci�n que verifica si un usuario representado con tu email existe 
 * o no en el sistema.
 * @param $email
 * @return boolean, true o false en caso de existir o no en el sistema. 
 */
function existeUsuario($email){
	
}

/**
 * Funci�n que envia a un usuario determinado un email de confirmaci�n para 
 * poder validar su cuenta y asignar valor "activo" a dicho usuario.
 * @param $email
 * @return No return
 */
function enviarEmailValidacion($email){
	
}
?>