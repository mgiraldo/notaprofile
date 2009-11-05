<?php
require_once('DAO.php');
require_once('PPUpload.php');

class Work {

	var $data;
	
	function processNewImage( $path, $type ){
		global $app;
		if(  !is_dir( $app["siteroot"] . $path ) && file_exists( $app["siteroot"] . $path ) ){
			switch( $type ){
				case 1:
					$PPUpload :: resize( $path, "396x270" );
					$PPUpload :: resize( $path, "235x160", "_n" );
					$PPUpload :: resize( $path, "156x106", "_s" );
					$PPUpload :: resize( $path, "65x44", "_xs" );
					break;
				case 2:
					$PPUpload :: resize( $path, "200x296" );
					break;
			}
			return true;
		} else {
			return false;
		}
		return true;
	}
	function save ($distributor_id,$country_id,$name,$original_name,$description,$date,$type_id,$photo_file,$poster_file,$director_list,$actor_list,$screenwriter_list,$music_list,$external_url,$buy_amazon,$categories,$prev_photo="",$prev_poster="",$chk_photo=NULL,$chk_poster=NULL,$id=NULL) {
		global $app;
		$photo = "";
		$poster = "";
		$name = $this->DAO->titleCase($name);

		$photo = $PPUpload :: checkAndUpload( $photo_file, "works", $prev_photo, $chk_photo );
		$this -> processNewImage( $app["imgroot"] . $app["photoroot"] . "works/" . $photo, 1 );

		$poster = $PPUpload :: checkAndUpload( $poster_file, "works", $prev_poster, $chk_poster );
		$this -> processNewImage( $app["imgroot"] . $app["photoroot"] . "works/" . $poster, 2 );

		if ($id==NULL) {
			$sql = sprintf("INSERT INTO work (distributor_id,country_id,name,original_name,description,date,type_id,photo,poster,external_url,buy_amazon,date_created) VALUES (%d,%d,'%s','%s','%s','%s',%d,'%s','%s','%s','%s','%s')",
			$distributor_id,
			$country_id,
			DAO::escape_str( $name ),
			DAO::escape_str( $original_name ),
			DAO::escape_str( $description ),
			DAO::escape_str( $date ),
			$type_id,
			DAO::escape_str( $photo ),
			DAO::escape_str( $poster ),
			DAO::escape_str( $external_url ),
			DAO::escape_str( $buy_amazon ),
			date("Y-m-d H:i:s")
			);
		} else {
			$sql = sprintf("UPDATE work SET distributor_id=%d, country_id=%d, name='%s', original_name='%s', description='%s', date='%s', type_id=%d, photo='%s', poster='%s', external_url='%s', buy_amazon='%s' WHERE id = %d",
			$distributor_id,
			$country_id,
			DAO::escape_str( $name ),
			DAO::escape_str( $original_name ),
			DAO::escape_str( $description ),
			DAO::escape_str( $date ),
			$type_id,
			DAO::escape_str( $photo ),
			DAO::escape_str( $poster ),
			DAO::escape_str( $external_url ),
			DAO::escape_str( $buy_amazon ),
			$id
			);
		}
		$this->DAO->doSQL($sql);
		if ($id==NULL) $id = $this->DAO->lastId();

		$this -> unsetArtists( $id );
		$this -> setDirectors( $id, $director_list );
		$this -> setActors( $id, $actor_list );
		$this -> setScreenWriters( $id, $screenwriter_list );
		$this -> setMusic( $id, $music_list );
		// el artista

		// si es pelicula metemos el director y los actores
		// solo está implementado el director (NOTA!!: para actores va a ser necesario recorrer un arreglo)
		// artist_type
		// 1: Director
		/*
		 if ($type_id==1) {
			$sql = sprintf("DELETE FROM ta_work_artist WHERE work_id=%d", $id );
			$this->DAO->doSQL($sql);

			// asociamos el director
			$sql = sprintf("INSERT INTO ta_work_artist (work_id,artist_id,artist_type_id) VALUES (%d,%d,%d)", $id, $artist_id, 1 );
			$this->DAO->doSQL($sql);
			}
			*/
		// actualizamos categorias
		$sql = sprintf("DELETE FROM ta_work_category WHERE work_id=%d", $id );
		$this->DAO->doSQL($sql);
		// ingresamos solo si hay categoria
		if (count($categories)>0) {
			$sql = "INSERT INTO ta_work_category (work_id,category_id) VALUES ";
			$cat_array = array();
			foreach ($categories as $cat) array_push($cat_array,sprintf("(%d,%s)" ,$id, $cat));
			$sql .= implode(",",$cat_array);
			$this->DAO->doSQL($sql);
		}
		return $id;
	}
	function delete ($id) {
		// borrar la foto
		$sql = sprintf("SELECT photo FROM work WHERE id = %s",
		mysql_real_escape_string(stripslashes($id))
		);
		$q = $this->DAO->parseQuery($this->DAO->doSQL($sql));
		if ($q[0]["photo"]!="") $this->upload->thereCanOnlyBeOne($q[0]["photo"],"works");
		$sql = sprintf("DELETE FROM ta_work_category WHERE work_id = %s",
		mysql_real_escape_string(stripslashes($id))
		);
		$this->DAO->doSQL($sql);
		$sql = sprintf("DELETE FROM work WHERE id = %s",
		mysql_real_escape_string(stripslashes($id))
		);
		$q = $this->DAO->doSQL($sql);
		return $q;
	}
	function deleteCategory ($id) {
		$sql = sprintf("DELETE FROM ta_work_category WHERE category_id = %s",
		mysql_real_escape_string(stripslashes($id))
		);
		$this->DAO->doSQL($sql);
		$sql = sprintf("DELETE FROM category WHERE id = %s",
		mysql_real_escape_string(stripslashes($id))
		);
		$q = $this->DAO->doSQL($sql);
		return $q;
	}
	function deleteArtist ($id) {
		$sql = sprintf("DELETE FROM ta_work_artist WHERE artist_id = %s",
		mysql_real_escape_string(stripslashes($id))
		);
		$this->DAO->doSQL($sql);
		$sql = sprintf("DELETE FROM artist WHERE id = %s",
		mysql_real_escape_string(stripslashes($id))
		);
		$q = $this->DAO->doSQL($sql);
		return $q;
	}
	function distributorHasChildren( $distributor_id ){
		$sql = "SELECT COUNT(*) cnt FROM `work` WHERE distributor_id = {$distributor_id};";
		$q = $this -> DAO -> parseQuery( $this -> DAO -> doSQL( $sql ) );
		return $q[0]["cnt"];
	}
	function deleteDistributor ($id) {
		$sql = sprintf( "DELETE FROM distributor WHERE id = %d", mysql_real_escape_string( stripslashes( $id ) ) );
		$this->DAO->doSQL($sql);
		return $q;
	}
	function unsetArtists( $id ){
		if( (Integer) $id ){
			$sql = "DELETE FROM ta_work_artist WHERE work_id = " . $id;
			$this -> DAO -> doSQL( $sql );
		}
		return true;
	}
	function setDirectors( $id, $director_list ){
		if( $id && is_array($director_list) ){
			foreach ( $director_list as $director_id ){
				$this -> addArtist( $director_id, $id, 1 );
			}
		}
		return false;
	}
	function setActors( $id, $actor_list ){
		if( $id && is_array($actor_list) ){
			foreach ( $actor_list as $actor_id ){
				$this -> addArtist( $actor_id, $id, 2 );
			}
		}
		return false;
	}
	function setScreenWriters( $id, $artist_list ){
		if( $id && is_array($artist_list) ){
			foreach ( $artist_list as $artist_id ){
				$this -> addArtist( $artist_id, $id, 3 );
			}
		}
		return false;
	}
	function setMusic( $id, $music_list ){
		if( $id && is_array($music_list) ){
			foreach ( $music_list as $music_id ){
				$this -> addArtist( $music_id, $id, 4 );
			}
		}
		return false;
	}
	function addArtist( $artist_id, $work_id, $artist_type_id ){
		$sql = sprintf( "INSERT INTO ta_work_artist ( artist_id, work_id, artist_type_id ) VALUES ( %d, %d, %d )", $artist_id, $work_id, $artist_type_id );
		$this -> DAO -> doSQL( $sql );
	}
	function getArtistIdsByType( $id, $type_id ){
		if( (Integer)$id && (Integer)$type_id ) {
			$sql = "SELECT a.id
				FROM artist a
				JOIN ta_work_artist taat ON a.id = taat.artist_id && artist_type_id = " . $type_id . " && work_id = " . $id . ";";
			$q = $this -> DAO -> parseQuery( $this ->  DAO -> doSQL( $sql ) );
			$aux = Array();
			foreach( $q as $row ) $aux[] = $row["id"];
			return $aux;
		}
		return false;
	}
	function makeLink( $data ){
		global $app;
		return $app["url"] . "work/" . $data['id'] . "/" . str_replace(array(" ",",",".","_","/","'","\""),"-",$data['name']) . "/";
	}
	function getRandom(  ){
		$sql = "SELECT * FROM work ORDER BY RAND() LIMIT 1";
		$q = $this -> DAO -> doSQLAndReturn( $sql );
		$q[0]["link"] = $this -> makeLink( $q[0] );
		return $q[0];
	}
}
?>