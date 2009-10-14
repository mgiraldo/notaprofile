<?php

/**
 * Clase encargada de manejar la base de datos.
 * @author DISE3320 - 20092
 */
class DAO{
	
	
	/**
	 * Variable que representa la conexión con el servidor de la base de datos
	 * @var unknown_type
	 */
	static $conn;

//----------------------------------------------------------------------------------------------
// Constructor
//----------------------------------------------------------------------------------------------	
	
	/**
	 * Constructor de la clase DAO. Se encarga de instanaciar la variable app y crear una conexión con el servidor de la bsae de datos.
	 * @return No return
	 */
	function __construct() {
		DAO::connect();
	}
	
//----------------------------------------------------------------------------------------------
// Funciones que corresponden a consultas en la base de datos
//----------------------------------------------------------------------------------------------	
	
	/**
	 * Este método se encarga de establecer la conexión con el servudir de la base de datos
	 * @return unknown_type
	 */
	private static function connect(){
		global $app;
		DAO::$conn = mysql_connect($app['dbhost'], $app['dbuser'], $app['dbpassword']) or die(mysql_error("Error connecting to MySQL Engine"));
		mysql_select_db($app['db'], DAO::$conn)or die(mysql_error("Error selecting '".$app['db']."' database"));
	} 
	
	/**
	 * Este metodo retorna la conexión con el servidor de la base de datos. Si no existe una conexión, la crea.
	 * @return unknown_type
	 */
	public static function getConn(){
		global $app;
		if( !DAO::$conn ) DAO::connect();
		return DAO::$conn;
	}
	
	/**
	 * Este metodo se encarga de convertir en un arreglo el resultado de una consulta SQL
	 * @param unknown_type $q
	 * @return unknown_type
	 */
	public static function parseQuery ($q) {
		$r = array();
		while ($row = mysql_fetch_assoc($q)) $r[] = $row;
		return $r;
	}
	
	/**
	 * Este metodo se encarga de ejecutar una sentencia SQL ingresada por parametro en la base de datos 
	 * @param unknown_type $sql
	 * @return unknown_type
	 */
	public static function doSQL ( $sql ) {
		
		global $app;
		$conn = DAO::getConn();
		$r = mysql_query( $sql, $conn );
		return $r;
		
	}
	
	/**
	 * Escapa una cadena para que sea segura para ingresar en base de datos.
	 * @param String $str
	 * @return String
	 */
	public static function escape_str ($str) {
		return mysql_real_escape_string(stripslashes($str));
	}

}

?>