<?php
require_once 'DAO.php';

/**
 * Función que procesa el inicio (login/registro) al sistema. 
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
 * Función que agrega un nuevo usuario al sistema.
 * @param $email Email del nuevo usuario
 * @param $clave Clave que aigna el usuario a su cuenta
 * @param $reclave Confirmación de la clave
 * @return no return
 */
function registrarUsuario($email, $clave, $reclave){
	

}

/**
 * Función que valida un usuario en el sistema e inicia su sesión.
 * @param $email
 * @param $clave
 * @return boolean, true o false en caso de que los datos sean correctos o no.
 */
function validarUsuario($email, $clave){
	
}

/**
 * Función que verifica si un usuario representado con tu email existe 
 * o no en el sistema.
 * @param $email
 * @return boolean, true o false en caso de existir o no en el sistema. 
 */
function existeUsuario($email){
	
}

/**
 * Función que envia a un usuario determinado un email de confirmación para 
 * poder validar su cuenta y asignar valor "activo" a dicho usuario.
 * @param $email
 * @return No return
 */
function enviarEmailValidacion($email){
	
}
?>