<?php
class DAO {

	static $conn;

	/**
	 * Manejo de Base de datos
	 *
	 * @return DAO
	 */
	function __construct() {
		global $app;
		DAO::$conn = mysql_connect($app['dbhost'], $app['dbuser'], $app['dbpassword']);
		if (DAO::$conn === false){
			die("Error connecting to MySQL Engine");
		} else if (mysql_select_db($app['db'], DAO::$conn) === false){
			die("Error selecting '".$app['db']."' database");
		}
	}

	public static function &getConn( ){
		global $app;
		if( !DAO::$conn ) DAO::$conn = mysql_connect($app['dbhost'], $app['dbuser'], $app['dbpassword']);
		if (DAO::$conn === false){
			die("Error connecting to MySQL Engine");
		} else if (mysql_select_db($app['db'], DAO::$conn) === false){
			die("Error selecting '".$app['db']."' database");
		}
		return DAO::$conn;
	}

	/**
	 * Convierte un Resource ID en un Array con los datos en cada item
	 *
	 * @param Resource ID $q
	 * @return Array
	 */
	public static function parseQuery ($q) {
		$r = array();
		while ($row = mysql_fetch_assoc($q)) $r[] = $row;
		return $r;
	}
	/**
	 * Ejecuta una sentencia SQL y devuelve el Resource ID
	 *
	 * @param String $sql
	 * @return Resource ID
	 */
	public static function doSQL ( $sql ) {
		global $app;
		// problemas de tildes se resuelven con la siguiente linea segun http://www.adviesenzo.nl/examples/php_mysql_charset_fix/
		//mysql_query("SET NAMES 'utf8'"); // no funciono...
		if( $app["debug"] ){
			$fp = fopen( $app['siteroot'] . "/sql.log", "a+" );
			fwrite( $fp, (str_replace( "\n", " ", str_replace( "\r", " ", $sql) ) . "\n") );
			fclose( $fp );
		}
		$conn = DAO::getConn();
		$r = mysql_query( $sql, $conn );
		if(!$r){
			if( mysql_errno() ){
				echo "SQL ERROR:<br>SQL=" . $sql . "<br>" . mysql_errno() . ": " . mysql_error() . "<br>Attempting repair...<br>";
				// trata de corregirse si es un SELECT
				if ( strpos( strtoupper( $sql ),"SELECT") !== false ) {
					// saca la tabla
					$ini = strpos(strtoupper($sql), "FROM ");
					if ($ini !== false) {
						$firstspace = strpos($sql, " ", $ini+5);
						$table = substr($sql, $ini+5, $firstspace-($ini+5));
						$repair = mysql_query(" REPAIR TABLE " . $table, $conn);
					}
				}
				die();
			}
		}
		if ( strpos( strtoupper( $sql ),"UPDATE") !== false ||
			strpos( strtoupper( $sql ),"INSERT") !== false ||
			strpos( strtoupper( $sql ),"REPLACE") !== false ||
			strpos( strtoupper( $sql ),"DELETE") !== false ){
			$r = mysql_affected_rows();
		}
		return $r;
	}
	/**
	 * Ejecuta un query y retorna su valor en un array
	 *
	 * @param String $sql Query a ejecutar
	 * @return Array
	 */
	public static function doSQLAndReturn( $sql ){
		return DAO::parseQuery( DAO::doSQL( $sql ) );
	}
	/**
	 * Verifica si en una tabla existe un campo con el valor pasado
	 *
	 * @param String $table
	 * @param String $field
	 * @param Mixed $value
	 * @param Int $id
	 * @return Array
	 */
	public static function exists ($table, $field, $value, $id = -1) {
		$sql = "SELECT id FROM " . $table . " WHERE " . $field . " = " . $value . " AND id <> " . $id;
		$q = DAO::doSQLAndReturn( $sql );
		return (count($q) > 0) ? $q[0]['id'] : 0 ;
	}
	/**
	 * Obtiene el id del último registro creado
	 *
	 * @return Int
	 */
	public static function lastId () {
		return mysql_insert_id();
	}
	/**
	 * Ejecuta un Begin
	 *
	 * @return Resource ID
	 */
	function doBegin () {
		$conn = DAO::getConn();
		return mysql_query("BEGIN",$conn);
	}
	/**
	 * ejecuta un commit
	 *
	 * @return Resource ID
	 */
	function doCommit () {
		$conn = DAO::getConn();
		return mysql_query("COMMIT",$conn);
	}
	/**
	 * Ejecuta un rollback
	 *
	 * @return Resource ID
	 */
	function doRollback () {
		$conn = DAO::getConn();
		return mysql_query("ROLLBACK",$conn);
	}
	/**
	 * Escapa una cadena para que sea segura para ingresar en base de datos
	 *
	 * @param String $str
	 * @return String
	 */
	public static function escape_str ($str) {
		return mysql_real_escape_string(stripslashes($str));
	}
	/**
	 * Pone en mayúsculas todas las iniciales de las palabras 
	 *
	 * @param String $str
	 * @return String
	 */
	function titleCase ($str) {
		/*
		 $arr = split(" ",$str);
		 $tmp = array();
		 foreach ($arr as $word) {
			$tmp[] = ucfirst($word);
			}
			return implode(" ",$tmp);
			*/
		return $str;
	}
	/**
	 * Devuelve una estructura en arbol de una tabla con un campo parent_id
	 *
	 * @param String $table_name
	 * @param Int $id_field
	 * @param String $parent_field
	 * @param int $id
	 * @return Array
	 */
	function getTree( $table_name, $id_field, $parent_field, $id = 0 ){
		$tree = Array();
		$sql = "SELECT {$id_field}, {$parent_field}, name FROM {$table_name} WHERE {$parent_field} = {$id}";
		$q = DAO::doSQLAndReturn( $sql );
		foreach( $q as $row ){
			$tree[] = Array(
				$id_field => $row[$id_field],
				"name" => $row["name"],
				$parent_field => $row[$parent_field],
				"children" => DAO::getTree( $table_name, $id_field, $parent_field, $obj -> {$id_field} )
			);
		}
		return $tree;
	}
}
?>