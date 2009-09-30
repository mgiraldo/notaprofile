<?php

require_once('DAO.php');
require_once('PPUpload.php');
require_once('User.php');

class Viral {

	public static $app;
	var $data;

	function Viral () {
	}

	public static function getData ($id) {
		$conn = DAO::getConn();
		$sql = sprintf("SELECT p.*,u.name FROM `photo` AS p, user AS u WHERE u.id=p.user_id AND p.id = %s",
						(mysql_real_escape_string($id, $conn))
					);
		$q = DAO::parseQuery(DAO::doSQL($sql));
		if (count($q)>0) {
			$q[0]['views'] = $q[0]['views'] + 1;
			$sql = sprintf("UPDATE `photo` SET views = %s WHERE id = %s",
								(mysql_real_escape_string($q[0]['views'], $conn)),
								(mysql_real_escape_string($id, $conn))
							);
			DAO::doSQL($sql);
		}
		return $q[0];
	}
	
	public static function getViralData ($id) {
		global $app;
		$conn = DAO::getConn();
		$sql = sprintf("SELECT v.* FROM `viral` AS v WHERE v.id=%s",
						(mysql_real_escape_string($id, $conn))
					);
		$q = DAO::parseQuery(DAO::doSQL($sql));
		if (count($q)>0) {
			$q[0]['views'] = $q[0]['views'] + 1;
			$sql = sprintf("UPDATE `viral` SET views = %s WHERE id = %s",
								(mysql_real_escape_string($q[0]['views'], $conn)),
								(mysql_real_escape_string($id, $conn))
							);
			DAO::doSQL($sql);
		} else {
			return false;
		}
		$q[0]['pid'] = str_replace('.jpg','',$q[0]['photo']);
		return $q[0];
	}
	
	public static function getAll ($sort='date',$all=0) {
		$order = 'ORDER BY p.created_date DESC';
		$hidden = '';
		if ($sort!='date') $order = 'ORDER BY p.views DESC';
		if ($all==0) $hidden = ' AND p.hidden_flag=0 ';
		$sql = "SELECT p.*,u.name,u.email FROM `photo` AS p, user AS u WHERE u.id=p.user_id $hidden $order";
		$q = DAO::parseQuery(DAO::doSQL($sql));
		return $q;
	}
	
	public static function upload ($field) {
		global $app;
		// las camaras canon se cagan las fotos
		$filename = PPUpload::checkAndUpload ($field,$app['photoroot'],"","");
		$e = PPUpload::resizeViral($filename);
		if ($e==-1) {
			return "error_notimage";
		} else if ($e==-2) {
			return "error_nofile";
		} else {
			return $e;
		}
	}
	
	public static function burn ($name, $data) {
		global $app;
		
		$fp = fopen( $app['siteroot'] . $app['photoroot'] . $name, 'wb' );
		fwrite( $fp, $data );    
		fclose( $fp );
		
		return $app['siteroot'] . $app['photoroot'] . $name;
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

	public static function burnOld ($photo) {
		$full_width = 450;
		$full_height = 300;
		
		$filename = $photo['filename'];
		$w = intval($photo['width']);
		$h = intval($photo['height']);
		
		// create the image with desired width and height
		
		$img = imagecreatetruecolor($w, $h);
		
		// now fill the image with blank color
		// do you remember i wont pass the 0xFFFFFF pixels 
		// from flash?
		imagefill($img, 0, 0, 0xFFFFFF);
		
		$rows = 0;
		$cols = 0;
		
		// now process every POST variable which
		// contains a pixel color
		for($rows = 0; $rows < $h; $rows++){
			// convert the string into an array of n elements
			for($cols = 0; $cols < $w; $cols++){
				// get the single pixel color value
				$value = $photo['pxmatrix'][$rows][$cols];
				// if value is not empty (empty values are the blank pixels)
				if($value != ""){
					// get the hexadecimal string (must be 6 chars length)
					// so add the missing chars if needed
					$hex = $value;
					while(strlen($hex) < 6){
						$hex = "0" . $hex;
					}
					// convert value from HEX to RGB
					$r = hexdec(substr($hex, 0, 2));
					$g = hexdec(substr($hex, 2, 2));
					$b = hexdec(substr($hex, 4, 2));
					// allocate the new color
					// N.B. teorically if a color was already allocated 
					// we dont need to allocate another time
					// but this is only an example
					$test = imagecolorallocate($img, $r, $g, $b);
					// and paste that color into the image
					// at the correct position
					imagesetpixel($img, $cols, $rows, $test);
				}
			}
		}
		
		// burn the image!
		return imagejpeg($img,$app['siteroot'] . $app['photoroot'] . $photo['part'] . "_" . $filename,100);
	}
	
	public static function mosaic ($data) {
		global $app;
		$filename = $data['filename'];
		$path = $app['siteroot'] . $app['photoroot'];
		
		if (file_exists($path.'0_'. $filename) && file_exists($path.'1_'. $filename) && file_exists($path.'2_'. $filename) && file_exists($path.'3_'. $filename) && file_exists($path.'4_'. $filename) && file_exists($path.'5_'. $filename) && file_exists($path.'6_'. $filename) && file_exists($path.'7_'. $filename) && file_exists($path.'8_'. $filename) && file_exists($path.'9_'. $filename)) {
			// los archivos existen
			$command = $app['convert_path'] .' -size '. $app['full_size'] . ' ';
			$command .= '-page +0+0 '.$path.'0_'. $filename . ' ';
			$command .= '-page +0+30 '.$path.'1_'. $filename . ' ';
			$command .= '-page +0+60 '.$path.'2_'. $filename . ' ';
			$command .= '-page +0+90 '.$path.'3_'. $filename . ' ';
			$command .= '-page +0+120 '.$path.'4_'. $filename . ' ';
			$command .= '-page +0+150 '.$path.'5_'. $filename . ' ';
			$command .= '-page +0+180 '.$path.'6_'. $filename . ' ';
			$command .= '-page +0+210 '.$path.'7_'. $filename . ' ';
			$command .= '-page +0+240 '.$path.'8_'. $filename . ' ';
			$command .= '-page +0+270 '.$path.'9_'. $filename . ' ';
			$command .= '-mosaic ' . $path . $filename;
			
			system($command);
			
			unlink($path.'0_'. $filename);
			unlink($path.'1_'. $filename);
			unlink($path.'2_'. $filename);
			unlink($path.'3_'. $filename);
			unlink($path.'4_'. $filename);
			unlink($path.'5_'. $filename);
			unlink($path.'6_'. $filename);
			unlink($path.'7_'. $filename);
			unlink($path.'8_'. $filename);
			unlink($path.'9_'. $filename);
			
			return PPUpload::resizeViral($filename);
		} else {
			// los archivos no existen completos
			return -1;
		}
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
	
	public static function moderate ($id,$value) {
		$conn = DAO::getConn();
		if (!is_numeric($id) || !is_numeric($value)) {
			return false;
		}
		$sql = sprintf("UPDATE `photo` SET hidden_flag = %s WHERE id = %s",
						(mysql_real_escape_string($value, $conn)),
						(mysql_real_escape_string($id, $conn))
					);
		DAO::doSQL($sql);
		return true;
	}
	
	public static function send ($name,$email,$friends,$photo,$background,$character,$song,$message,$id=-1) {
		$id1 = Viral::addUser($name, $email);
		if ($id1==-1) {
			return -1;
		}
		if ($id==-1 || $id==0) {
			$photo_id = Viral::save($id1,$photo,$song,$character,$background,$message);
		} else {
			$photo_id = $id;
		}
		$friends = str_replace(" ","",$friends);
		$friend_array = split(",",$friends);
		foreach ($friend_array as $f_email) {
			if ($f_email!='') {
				Viral::addUser("", $f_email,$id1);
				if ($id==-1 || $id==0) {
					Viral::sendMessageTo($name,$f_email,$photo_id,$id1,$message);
				} else {
					Viral::resendMessageTo($name,$f_email,$photo_id,$id1,$message);
				}
			}
		}
		if ($id==-1 || $id==0) {
			Viral::sendMessageFrom($name,$email,$photo_id);
		} else {
			Viral::resendMessageFrom($name,$email,$photo_id);
		}
		return $photo_id;
	}
	
	public static function sendMessageFrom ($name,$email,$photo_id) {
		global $app;
		$reg = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		if(!eregi($reg, $email)) {
			// email chimbo
			return -1;
		}
		// mandar el mail
		$tema = "Te has lanzado a la fama! Guarda este email para poder ver tu video.";
		$link = $app['url'] . "f/" . $photo_id;
		//$link = $app['url'] . "?f=" . $photo_id;
		$contenido = "Hola, $name.\r\n\r\nTe felicitamos por haberte Lanzado a la Fama con PEPSI.\r\n\r\nHaz clic sobre el link para que puedas disfrutar de tu video cada vez que lo desees.\r\n\r\n";
		$contenido .= $link . "\r\n\r\n";
		$contenido .= "Nos vemos en http://www.pepsimundo.com\r\n\r\n";

		Viral::sendMail($email,$email,$tema,$contenido);
		
		return 1;
	}
	
	public static function resendMessageFrom ($name,$email,$photo_id) {
		global $app;
		$reg = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		if(!eregi($reg, $email)) {
			// email chimbo
			return -1;
		}
		// mandar el mail
		$tema = "Guarda este email para poder ver el video.";
		$link = $app['url'] . "f/" . $photo_id;
		//$link = $app['url'] . "?f=" . $photo_id;
		$contenido = "Hola, $name.\r\n\r\nHaz clic sobre el link para que puedas disfrutar del video cada vez que lo desees.\r\n\r\n";
		$contenido .= $link . "\r\n\r\n";
		$contenido .= "Nos vemos en http://www.pepsimundo.com\r\n\r\n";

		Viral::sendMail($email,$email,$tema,$contenido);
		
		return 1;
	}
	
	public static function sendMessageTo ($name,$email,$photo_id,$fromid,$message) {
		global $app;
		$reg = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		if(!eregi($reg, $email)) {
			// email chimbo
			return -1;
		}
		// mandar el mail
		$sql = "SELECT name,email FROM user WHERE id = $fromid";
		$q = DAO::doSQLAndReturn($sql);
		$fromemail = $q[0]['email'];
		$fromname = $q[0]['name'];
		$tema = $name . " se ha lanzado a la fama. Mira su video.";
		$link = $app['url'] . "f/" . $photo_id;
		//$link = $app['url'] . "?f=" . $photo_id;
		$contenido = "Quiero que sepas que me he Lanzado a la Fama. Para que puedas ver mi video haz clic en el siguiente link:\r\n\r\n";
		$contenido .= $link . "\r\n\r\n";
		$contenido .= "Espero que lo disfrutes y Lánzate a la Fama tu también.\r\n\r\n";
		if ($message != "") {
			$contenido .= "Mensaje de $name:\r\n" . $message . "\r\n";
		}

		Viral::sendMail($email,$email,$tema,$contenido);
		
		return 1;
	}
	
	public static function resendMessageTo ($name,$email,$photo_id,$fromid,$message) {
		global $app;
		$reg = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		if(!eregi($reg, $email)) {
			// email chimbo
			return -1;
		}
		// mandar el mail
		$sql = "SELECT name,email FROM user WHERE id = $fromid";
		$q = DAO::doSQLAndReturn($sql);
		$fromemail = $q[0]['email'];
		$fromname = $q[0]['name'];
		$tema = $name . " quiere que veas este video.";
		$link = $app['url'] . "f/" . $photo_id;
		//$link = $app['url'] . "?f=" . $photo_id;
		$contenido = $name . " quiere que veas este video que ha encontrado. Para que puedas ver mi video haz clic en el siguiente link:\r\n\r\n";
		$contenido .= $link . "\r\n\r\n";
		$contenido .= "Espero que lo disfrutes y Lánzate a la Fama!\r\n\r\n";
		if ($message != "") {
			$contenido .= "Mensaje de $name:\r\n" . $message . "\r\n";
		}

		Viral::sendMail($email,$email,$tema,$contenido);
		
		return 1;
	}
	
	public static function activate ($email) {
		$reg = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		if(!eregi($reg, $email)) {
			// email chimbo
			return -1;
		}
		// mandar el mail
		$sql = "UPDATE user SET active_flag = 1 WHERE email = '$email'";
		DAO::doSQL($sql);
		return 1;
	}
	
	public static function sendMail($to_name,$to_email,$subject,$msg) {
		/***************************
		include_once("Mail.php");
		
		$recipients = $app['siteemail'];
		
		$headers["From"]    = $app['siteemail'];
		$headers["To"]      = $to_email;
		$headers["Subject"] = $subject;
		$headers["Content-type"] = "text/plain; charset=utf-8";
		
		$body = $msg;
		
		$params["host"] = $app['smtp_host'];
		$params["port"] = $app['smtp_port'];
		$params["auth"] = $app['smtp_auth'];
		$params["username"] = $app['smtp_username'];
		$params["password"] = $app['smtp_password'];
		
		// Create the mail object using the Mail::factory method
		$mail_object =& Mail::factory("smtp", $params);
		
		return $mail_object->send($recipients, $headers, $body);
		***************************/
		global $app;
		$headers = "From: " . $app['siteemail'] . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
		$body = $msg;
		mail($to_email,$subject,$body,$headers);
	}
	
	public static function addUser ($name, $email, $referrer=0) {
		$reg = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		$conn = DAO::getConn();
		if(eregi($reg, $email)) {
			$sql = sprintf("SELECT * FROM user WHERE email = '%s'",mysql_real_escape_string(stripslashes($email), $conn));
			$q = DAO::doSQLAndReturn($sql);
			if (count($q)==0) {
				// new user
				$flag = $flag == -1 ? 0 : $flag;
				$optin = $optin == -1 ? 0 : $optin;
				$sql = sprintf("INSERT INTO user (email,name,referrer_id,registered_date) VALUES ('%s','%s',%s,'%s')",
									mysql_real_escape_string(stripslashes($email), $conn),
									mysql_real_escape_string(stripslashes($name), $conn),
									mysql_real_escape_string(stripslashes($referrer), $conn),
									date("Y-m-d H:i:s")
								);
				DAO::doSQL($sql);
				$id = DAO::lastId();
			} else {
				// old or referred user
				$updates = array();
				if ($q[0]['name'] != $name && $name != '') $updates[] = "name='".mysql_real_escape_string(stripslashes($name), $conn)."'";
				if ($q[0]['email'] != $email) $updates[] = "email='".mysql_real_escape_string(stripslashes($email), $conn)."'";
				if (count($updates)>0) {
					$values = implode(',',$updates);
					$sql = "UPDATE user SET $values WHERE id=".$q[0]['id'];
					DAO::doSQL($sql);
				}
			}
			if (count($q)>0) {
				$id = $q[0]['id'];
			}
		} else {
			$id = -1;
		}
		return $id;
	}
	
}

?>