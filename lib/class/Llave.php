<?php

require_once('DAO.php');
require_once('PPUpload.php');

class Llave {
	
	function Llave () {
	}
	
	public static function getData ($id) {
		$conn = DAO::getConn();
		$sql = sprintf("SELECT * FROM llave WHERE id = %s",
						(mysql_real_escape_string($id, $conn))
					);
		$q = DAO::doSQLAndReturn($sql);
		if (count($q)>0) {
			$q[0]['views'] = $q[0]['views'] + 1;
			$sql = sprintf("UPDATE `llave` SET views = %s WHERE id = %s",
								(mysql_real_escape_string($q[0]['views'], $conn)),
								(mysql_real_escape_string($id, $conn))
							);
			DAO::doSQL($sql);
		}
		return $q[0];
	}
	
}

?>