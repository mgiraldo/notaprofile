<?php
require_once('DAO.php');
require_once('PPUpload.php');
class Usuario {
	
	var $DAO;
	var $upload;

	function Usuario(){
		$this->DAO = new DAO;
		$this->upload = new PPUpload;
	}

	function save( $email, $clave, $id = null ){
		if ($id==NULL) {
			$sql = sprintf("INSERT INTO usuario ( email, clave ) VALUES ( '%s','%s' )", DAO::escape_str($email), md5($clave) );
		} else {
			// solo cambiar si la clave cambia
			// sacar la clave de la base de datos
			// comparar con la clave que viene
			$sql = sprintf("UPDATE usuario SET email='%s', clave='%s' WHERE id = %d", DAO::escape_str($email), md5($clave), $id );
		}
		$r = $this->DAO->doSQL($sql);
		if ($id==NULL) $r = $this->DAO->lastId();
		return $r;
	}

	function getAll(){
		return $this -> DAO -> doSQLAndReturn( "SELECT * FROM usuario" );
	}

}
?>