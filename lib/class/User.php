<?php

require_once('DAO.php');

class User {

	var $app;
	var $data;
	var $DAO;

	function User () {
		session_start();
		$this->app = unserialize(APP);
		$this->DAO = new DAO();		
		if (!$this->isLoggedIn() && (array_key_exists("sid",$_GET) || array_key_exists("sid",$_POST))){
			$sss = array_key_exists("sid",$_GET) ? $_GET["sid"] : $_POST["sid"];
			session_destroy();
			session_id(trim($sss));
			session_start();	
			$_SESSION["_client_"] = array();
			$this->isLoggedIn();
		}
		if (!$this->logged){
			$_SESSION["_client_"] = array();
		}
	}

	function isLoggedIn(){
		$this->logged = (array_key_exists("_client_",$_SESSION) && intval($_SESSION["_client_"]["id"]) > 0);
		return $this->logged;
	}
	
	function checkPrivileges($level,$redir=true){
		$s = $this->app['url'] . $this->app['basefolder'] . "adm/login.php?d=" . date("Ymd");
		if (!$this->logged){
			if ($redir){
				$s .= "&b_url=".urlencode($_SERVER["REQUEST_URI"]);
				$s .= "&type=0";
				$s = "Location: " . $s;
				header($s);
				exit();
			} else {
				return false;
			}
		} else {
			if ($level > $_SESSION["_client_"]['type_id']) {
				if ($redir){
					$s .= "&b_url=".urlencode($_SERVER["REQUEST_URI"]);
					$s .= "&type=1";
					$s = "Location: " . $s;
					header($s);
					exit();
				} else {
					return false;
				}
			} else {
				return true;
			}
		}
	}
	
	function notOwner(){
		$s = $this->app['url'] . $this->app['basefolder'] . "adm/login.php?d=" . date("Ymd");
		$s .= "&b_url=".urlencode($_SERVER["REQUEST_URI"]);
		$s .= "&type=1";
		$s = "Location: " . $s;
		header($s);
		exit();
	}
	
	function getAll () {
		$lang = mysql_real_escape_string($lang);
		$sql = "SELECT * FROM `user` ORDER BY name ASC";
		$q = $this->DAO->parseQuery($this->DAO->doSQL($sql));
		return $q;
	}
	
	function getData ($id) {
		$sql = sprintf("SELECT * FROM `user` WHERE id = '%s'",
						(mysql_real_escape_string($id))
					);
		$q = $this->DAO->parseQuery($this->DAO->doSQL($sql));
		$r = (count($q)>0) ? $q[0] : array();
		$r['username'] == '' ? '<sin alias>' : $r['username'];
		return $r;
	}
	
	function getSID () {
		return session_name()."=".session_id();
	}
	
	function logout () {
		session_destroy();
		$this->logged = false;
		return 1;
	}

	function findByEmail($email) {
		$sql = 'SELECT *
				FROM user 
				WHERE email = ' . $this->DAO->getSQLValueString($email, 'text');
		$r = $this->DAO->parseQuery($this->DAO->doSQL($sql));
		if (count($r) == 0) {
			return false;
		}
		return $r[0];
	}
	
	function exists ($campo, $valor, $type, $id=null) {
		if (!$id) {
			$sql = sprintf('SELECT id FROM user where %s=%s',
				$campo,
				$this->DAO->getSQLValueString($valor, $type)
			);
		} else {
			$sql = sprintf('SELECT id FROM user where %s=%s AND id <> %s',
				$campo,
				$this->DAO->getSQLValueString($valor, $type),
				$this->DAO->getSQLValueString($id, 'int')
			);
		}
		$r = $this->DAO->parseQuery($this->DAO->doSQL($sql));
		return count($r)>0;
	}

	function hasValidChars($strString,$chars) {
		$strValidChars = $chars;
		$strChar;
		$blnResult = true;
		
		if (strlen($strString) == 0) return false;
		
		//  test strString consists of valid characters listed above
		for ($i = 0; $i < strlen($strString) && $blnResult == true; $i++) {
			$strChar = substr($strString,$i,1);
			if (!strpos($strValidChars,$strChar)) {
				$blnResult = false;
			}
		}
		return $blnResult;
	}

	function save ($email,$username,$password,$firstname='',$lastname='',$identification='',$gender='',$preference='',$birth_date=0,$photo='',$address='',$city='',$phone='',$mobile='',$flag_opt_in=1,$r_id=0,$id=NULL) {
		$app = unserialize(APP);
		$r = 0;
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			// email chimbo
			return -3;
		}
		if(!$this->hasValidChars($username,"0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_@.")) {
			// usuario no valido
			return -4;
		}
		if ($id==NULL) {
			// creando...
			// verificar que no exista el email
			if ($this->existe('email',$email,'text')) {
				return -1;
			}
			if ($this->existe('username',$username,'text')) {
				return -2;
			}
			// insertar el usuario con activo = 0
			$last_ip = $_SERVER['REMOTE_ADDR'];
			$last_browser = $_SERVER['HTTP_USER_AGENT'];
			$date = date("Ymd");
			$token = md5(uniqid(rand(), true));
			$sql = sprintf("INSERT INTO user (token,firstname,lastname,username,email,password,identification,gender,preference,birth_date,photo,address,city,phone,mobile,flag_opt_in,registration_date,last_ip,last_browser,last_date,r_id) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s',%s,'%s','%s','%s','%s','%s',%s,%s,'%s','%s',%s,%s)",
					mysql_real_escape_string($token),
					mysql_real_escape_string($firstname),
					mysql_real_escape_string($lastname),
					mysql_real_escape_string($username),
					mysql_real_escape_string($email),
					mysql_real_escape_string(md5($password)),
					mysql_real_escape_string($identification),
					mysql_real_escape_string($gender),
					mysql_real_escape_string($preference),
					mysql_real_escape_string($birth_date),
					mysql_real_escape_string($photo),
					mysql_real_escape_string($address),
					mysql_real_escape_string($city),
					mysql_real_escape_string($phone),
					mysql_real_escape_string($mobile),
					mysql_real_escape_string($flag_opt_in),
					mysql_real_escape_string($date),
					mysql_real_escape_string($last_ip),
					mysql_real_escape_string($last_browser),
					mysql_real_escape_string($date),
					mysql_real_escape_string($r_id)
					);
			$this->DAO->doSQL($sql);
			$id = $this->DAO->lastId();
			
			$this->sendActivationEmail($email);

			$r = $id;
		} else {
			// actualizando...
			// verificar que la informacion este completa (requeridos)
			// verificar que no exista el email
			if ($this->existe('email',$email,'text',$id)) {
				return -1;
			}
			// verificar que no exista el usuario
			if ($this->existe('username',$username,'text',$id)) {
				return -2;
			}
			// sacamos la clave
			$q = $this->DAO->parseQuery($this->DAO->doSQL("SELECT password FROM user WHERE id = " . $id));
			$clave_actual = $q[0]['password'];
			$password = ($clave_actual != $password) ? md5($password) : $password;
			// actualizar los datos
			$sql = sprintf("UPDATE user SET 
						firstname='%s',
						lastname='%s',
						username='%s',
						email='%s',
						password='%s',
						identification='%s',
						gender='%s',
						preference='%s',
						birth_date=%s,
						photo='%s',
						address='%s',
						city='%s',
						phone='%s',
						mobile='%s',
						flag_opt_in=%s
						WHERE id=%s",
					mysql_real_escape_string($firstname),
					mysql_real_escape_string($lastname),
					mysql_real_escape_string($username),
					mysql_real_escape_string($email),
					mysql_real_escape_string($password),
					mysql_real_escape_string($identification),
					mysql_real_escape_string($gender),
					mysql_real_escape_string($preference),
					mysql_real_escape_string($birth_date),
					mysql_real_escape_string($photo),
					mysql_real_escape_string($address),
					mysql_real_escape_string($city),
					mysql_real_escape_string($phone),
					mysql_real_escape_string($mobile),
					mysql_real_escape_string($flag_opt_in),
					mysql_real_escape_string($id));
			$this->DAO->doSQL($sql);
	
			$r = $id;
		}
		return $r;
	}
	
	function sendActivationEmail ($email) {
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			// email chimbo
			return -1;
		}
		$data = $this->findByEmail($email);
		if (!$data) {
			// email no existe
			return -2;
		}
		// mandar el mail
		$id = $data['id'];
		$token = $data['token'];
		$email = $data['email'];
		$link = "{$this->app['url']}activar/". md5($id) . "-" . $token . "/";
		$tema = "Activa tu cuenta en Condones DUO";
		$contenido = "Hola!\r\n\r\n";
		$contenido .= "Te registraste en Condones DUO!\r\n\r\n";
		$contenido .= "Para activar tu cuenta debes hacer clic en el link a continuacion:";
		$contenido .= "\r\n\r\n$link\r\n\r\n";
		$contenido .= "SI NO ACTIVAS TU CUENTA NO PODRAS GANAR!\r\n";
		$contenido .= "Para mayor informacion lee los terminos y condiciones\r\n";
		$contenido .= "del concurso en www.condonesduo.com\r\n\r\n";
		$contenido .= "Guarda tu informacion en un lugar seguro!\r\n";
		$contenido .= "www.condonesduo.com\r\n\r\n";
		$contenido .= "[acentos y tildes omitidas voluntariamente]\r\n";

		$this->sendMail($email,$email,$tema,$contenido);
		
		return 1;
	}
	
	function sendMail($to_name,$to_email,$subject,$msg) {
		/***************************
		include_once("Mail.php");
		
		$recipients = $this->app['siteemail'];
		
		$headers["From"]    = $this->app['siteemail'];
		$headers["To"]      = $to_email;
		$headers["Subject"] = $subject;
		$headers["Content-type"] = "text/plain; charset=utf-8";
		
		$body = $msg;
		
		$params["host"] = $this->app['smtp_host'];
		$params["port"] = $this->app['smtp_port'];
		$params["auth"] = $this->app['smtp_auth'];
		$params["username"] = $this->app['smtp_username'];
		$params["password"] = $this->app['smtp_password'];
		
		// Create the mail object using the Mail::factory method
		$mail_object =& Mail::factory("smtp", $params);
		
		return $mail_object->send($recipients, $headers, $body);
		***************************/
		$headers = "From: " . $this->app['siteemail'] . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
		$body = $msg;
		mail($to_email,$subject,$body,$headers);
	}
	
}

?>