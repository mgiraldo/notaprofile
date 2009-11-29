<?php
require ('HTTP/Upload.php');

/**
 * Enter description here...
 *
 */
class PPUpload {
	/**
	 * HTTP_Upload
	 *
	 * @var HTTP_Upload
	 */
	static $upload = null;
		
	function PPUpload() {
		global $app;
		$upload = PPUpload::getHTTPUploadObject();
		$upload->setChmod(0644);
	}
	
	public static function &getHTTPUploadObject( ){
		if( !PPUpload::$upload ) PPUpload::$upload = new HTTP_Upload();
		PPUpload::$upload->setChmod(0644);
		return PPUpload::$upload;
	}
	/**
	 * Sube una imagen
	 *
	 * @param String $f El nombre del campo tipo file
	 * @param String $d Directorio al que se copiará el archivo
	 * @param String $name_conflict Forma en la que se asigna el nombre a los archivos copiados.
	 * @return String/Bool Nombre del archivo tal como se guardó, o false en caso de fallar
	 */
	public static function doImageUpload ($f,$d,$name_conflict='uniq') {
		global $app;
		if ( !file_exists( $app['siteroot'] . $d ) ) die( "No existe el directorio de destino para el archivo cargado!" );
		if ( $_FILES[$f]["size"] < 10 || $_FILES[$f]["error"] != 0  ) return false;
		$upload = PPUpload::getHTTPUploadObject();
		$file = $upload->getFiles($f);
		$props = $file->getProp();
		// validamos que sea imagen
		if (!strstr($props['type'],'image/')) {
			trigger_error("ERROR_NOT_IMAGE",E_USER_ERROR);
			return false;
		}
		// validamos que pese menos del limite
		if ($props['size'] > $app['photo_max_size']) {
			trigger_error("ERROR_IMAGE_SIZE",E_USER_ERROR);
			return false;
		}
		// validamos error pear
		if (PEAR::isError($file)) {
			trigger_error("ERROR_IMG_" . $file->getMessage(),E_USER_ERROR);
			return false;
		}
		// metemos en carpeta
		if ($file->isValid()) {
			$file->setName($name_conflict);
			$dest_dir = $app['siteroot'] . $d . DIRECTORY_SEPARATOR;

			$dest_name = $file->moveTo($dest_dir);
			/**
			if (!exif_imagetype($dest_dir . $dest_name)) {
				unlink($dest_dir . $dest_name);
				trigger_error("ERROR_NOT_IMAGE",E_USER_ERROR);
				return false;
			}
			/**/
			return $dest_name;
		} elseif ($file->isMissing()) {
			trigger_error("ERROR_NO_FILE",E_USER_ERROR);
			return false;
		} elseif ($file->isError()) {
			trigger_error("ERROR_IMG_" . $file->errorMsg(),E_USER_ERROR);
			return false;
		}
	}
	public static function doUpload( $field, $dest_dir, $name_conflict='uniq' ){
		global $app;
		if ( !file_exists( $app['siteroot'] . $dest_dir ) ) die( sprintf( "No existe el directorio de destino para el archivo cargado! (%s)", ($app['siteroot'] . $dest_dir) ) );
		if ( $_FILES[$field]["size"] < 10 || $_FILES[$field]["error"] != 0  ) {
			return false;
		}
		$upload = PPUpload::getHTTPUploadObject();
		$file = $upload->getFiles($field);
		//$props = $file->getProp();
		// validamos error pear
		if ( PEAR::isError( $file ) ) {
			trigger_error( "ERROR_PPUPLOAD_" . $file->getMessage(), E_USER_ERROR );
			return false;
		}
		// metemos en carpeta
		if ($file->isValid()) {
			$file->setName( $name_conflict );
			#			$dest_dir = $d . "/";
			$dest_name = $file->moveTo($app["siteroot"] . $dest_dir);
			return $dest_name;
		} elseif ($file->isMissing()) {
			trigger_error("ERROR_NO_FILE",E_USER_ERROR);
			return false;
		} elseif ($file->isError()) {
			trigger_error("ERROR_PPULOAD_" . $file->errorMsg(),E_USER_ERROR);
			return false;
		}
	}
	public static function thereCanOnlyBeOne ($f, $d) {
		global $app;
		if (file_exists($app['siteroot'] . $d . DIRECTORY_SEPARATOR . $f)) {
			unlink($app['siteroot'] . $d . DIRECTORY_SEPARATOR . $f);
		}
	}
	public static function delete( $filename, $directory ){
		global $app;
		
		$d = dir( $app["siteroot"] . $directory );
		while( false !== ($entry = $d->read())){
			$name = substr( $filename, 0, strpos( $filename, '.' ) );
			if( substr( $entry, 0, strlen($name) ) == $name ) @unlink( $d -> path . $entry );
		}
		#if ( file_exists( $directory . $filename ) ) unlink( $directory . $filename );
	}
	/**
	 * Copia un archivo y elimina otro, se usa para actualizar archivos. 
	 *
	 * @param String $field Nombre del campo POST
	 * @param String $folder Path absoluto a la carpeta en la que se va a trabajar, tanto para subir como para eliminar
	 * @param String $previous Nombre del archivo antiguo que será eliminado en caso de ser enviado uno para su remplazo
	 * @param Int $checked Si esta checkedo elimina El archivo antiguo aunque no se envíe uno para su remplazo
	 * @return String Nombre del nuevo Archivo o "" en caso que no halla archivo especificado.
	 */
	public static function checkAndUpload ( $field, $folder, $previous, $checked ) {
		$filename = "";
		#Eliminando archivo actual
		if (($_FILES[$field]["name"]!="" && $previous!="") || ($checked==1)){
			PPUpload::delete( $previous, $folder );
		}
		#Si se envío archivo se sube y se devuelve el nombre del nuevo archivo, si no y tampoco se ha enviado archivo entonces devuelve el nombre del archivo anterior.
		if ($_FILES[$field]["name"]!=""){
			$filename = PPUpload::doUpload($field,$folder);
		} elseif ($_FILES[$field]["name"]=="" && $checked==""){
			$filename = $previous;
		}
		return $filename;
	}
	public static function resizeViral ($filename) {
		/*
		
		-1 no es foto
		-2 archivo no existe
		
		*/
		// partiendo de un archivo de foto crea la original y postcard en el temp
		global $app;
		$source_name = substr($filename,0,strpos($filename,'.'));
		$source_dir = $app['siteroot'] . $app['photoroot'];
		// porque en windows .JPG == .jpg se renombra
		rename($source_dir . $filename, $source_dir . $filename . '.tmp');
		$source_full = $source_dir . $filename . '.tmp';
		$thumb_full = $source_dir . $source_name . "_t.jpg";
		$resize_iphone = $source_dir . $source_name . "_ip.jpg";
		$resize_full = $source_dir . $source_name . ".jpg";
		/**
		if (!exif_imagetype($source_full)) {
			//trigger_error("ERROR_NOT_IMAGE",E_USER_ERROR);
			unlink($source_full);
			return -1;
		}
		/**/
		// procedemos a tirarnos la foto original en JPG
		
		if (!file_exists($source_full)) {
			//trigger_error("ERROR_SOURCE_NOT_EXISTS",E_USER_ERROR);
			return -2;
		}
		
		$data = getimagesize($source_full);
		$width = $data[0];
		$height = $data[1];
		
		if ($width>$height) {
			$format = 1;//horiz
		} else if ($width==$height) {
			$format = 0;//cuad
		} else {
			$format = 2;//vert
		}
		
		$size = ' -size ' . $width.'x'.$height . ' ';
		
		/**/
		switch ($format) {
			case 1:
				$crop = ' -crop '.$height.'x'.$height.'+'.floor(($width-$height)/2).'+0! ';
				break;
			case 2:
				$crop = ' -crop '.$width.'x'.$width.'+0+'.floor(($height-$width)/2).'! ';
				break;
			default:
				$crop = '';
				break;
		}
		
		// primero crop... por alguna razon mediatemple no aguanta todo de una
		
		$command = $app['convert_path'] . $size . $source_full . $crop . '  -quality 100 +profile "*" ' . $thumb_full . '';
		//echo $command;
		system($command);
		
		// ahora tamanio thumb
		
		$command = $app['convert_path'] . ' ' . $thumb_full . ' -resize ' . $app['thumb_size'] . '  -quality 100 +profile "*" ' . $thumb_full . '';
		//echo $command;
		system($command);
		/**/
				
		$command = $app['convert_path'] . $size . $source_full . ' -resize ' . $app['full_size'] . '  -quality 100 +profile "*" ' . $resize_full . '';
		//echo $command;
		system($command);
		
		$command = $app['convert_path'] . $size . $source_full . ' -resize ' . $app['iphone_size'] . '  -quality 100 +profile "*" ' . $resize_iphone . '';
		//echo $command;
		system($command);
		
		/**/
		if (is_file($source_full)) {
			unlink($source_full);
		}
		/**/
		return $source_name ;
	}
	
	/**
	 * Coge la imagen pasada y si hay un sufijo hace una copia con el nuevo tamaño y si no hay una copia, entonces modifica la original. 
	 *
	 * @param String $path Ruta relativa a la imágen desde la raíz del sitio ej: img/fotos/foto1.jpg
	 * @param String $newsize Nuevo tamaño por ejemplo "50x50"
	 * @param String $sufix Sufijo si se pasa el sufijo la nueva imágen se llamará nombreoriginalsufijo.***
	 * @return String Nombre de la imágen original
	 */
	public static function resize( $path, $newsize, $sufix = "" ){
		global $app;
		$src_file_name = substr( $path, (strrpos( $path,'/')+1) );
		$src_dir_path = $app["siteroot"] . dirname( $path );
		$src_file_path = $src_dir_path . $src_file_name;
		$dest_file_name = substr( $src_file_name, 0, strpos( $src_file_name, '.' ) ) . $sufix . ".jpg";
		$dest_file_path = $src_dir_path . $dest_file_name;
		$src_size = getimagesize( $src_file_path );
		$src_size = $src_size[0] . "x" . $src_size[1];
		$command = sprintf( "%s -size %s %s -resize %s -quality 100 +profile \"*\" %s", $app["convert_path"], $src_size, $src_file_path, $newsize, $dest_file_path );
		$var = system( $command );
		return $dest_file_name;		
	}
	public static function makeSquare( $path, $newsize, $sufix = "" ){
		global $app;
		$src_file_name = substr( $path, (strrpos( $path,'/')+1) );
		$src_dir_path = $app["siteroot"] . dirname( $path );
		$src_file_path = $src_dir_path . $src_file_name;
		$dest_file_path = $src_dir_path . substr( $src_file_name, 0, strpos( $src_file_name, '.' ) ) . $sufix . ".jpg";
	
		list( $width, $height ) = getimagesize( $src_file_path );
		$src_size = $width . "x" . $height;
		
		if( $width > $height ) $t = "h";
		else if ( $height > $width ) $t = "v";
		else $t = "c";
		
		if( $t == "h" ) $crop = ' -crop '.$height.'x'.$height.'+'.intval(($width-$height)/2).'+0! ';
		else if( $t == "v" ) $crop = ' -crop '.$width.'x'.$width.'+0+'.intval(($height-$width)/2).'! ';
		
#		if( $t !== "c" ){
			$command = sprintf ( "%s -size %s %s %s -quality 100 +profile \"*\" %s", $app["convert_path"], $src_size, $src_file_path, $crop, $dest_file_path );
			$var = system( $command );
#		}

		list( $width, $height ) = getimagesize( $dest_file_path );
		$src_size = $width . "x" . $height;
		$command = sprintf( "%s -size %s %s -resize %s -quality 100 +profile \"*\" %s", $app["convert_path"], $src_size, $dest_file_path, $newsize, $dest_file_path );
		$var = system( $command );
		return $src_file_name;				
	}
}
?>