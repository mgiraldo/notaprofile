<?php
require_once('DAO.php');
require_once('PPUpload.php');

class Work {

	var $data;
	
	public static function upload ($field) {
		global $app;
		$filename = PPUpload::checkAndUpload ($field,$app['photoroot'],"","");
		//$e = PPUpload::resizeViral($filename);
		/*
		if ($e==-1) {
			return "error_notimage";
		} else if ($e==-2) {
			return "error_nofile";
		} else {
			return $e;
		}
		*/
		echo($filename);
		return $filename;
	}
	
	public static function save ($user_id,$photo,$music,$character,$background,$message) {
		$conn = DAO::getConn();
		$sql = sprintf("INSERT INTO viral (user_id,photo,music,`character`,background,message,created_date,views) VALUES (%s,'%s',%s,%s,%s,'%s','%s',%s)",
						(mysql_real_escape_string($user_id, $conn)),
						(mysql_real_escape_string($photo, $conn)),
						(mysql_real_escape_string($music, $conn)),
						(mysql_real_escape_string($character, $conn)),
						(mysql_real_escape_string($background, $conn)),
						(mysql_real_escape_string($message, $conn)),
						(date("Y-m-d H:i:s")),
						0
					);
		DAO::doSQL($sql);
		$r = DAO::lastId();
		return $r;
	}

	public static function delete ($id) {
		global $app;
		$conn = DAO::getConn();
		$sql = sprintf("SELECT photo FROM viral WHERE id = %s",
						(mysql_real_escape_string($id, $conn))
					);
		$q = DAO::parseQuery(DAO::doSQL($sql));
		if ($q[0]["photo"]!="") {
			PPUpload::thereCanOnlyBeOne($q[0]["photo"],$app['photoroot']);
		}
		$sql = sprintf("DELETE FROM viral WHERE id = %s",
						(mysql_real_escape_string($id, $conn))
					);
		DAO::doSQL($sql);
		return true;
	}
	
}
?>