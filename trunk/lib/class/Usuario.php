<?php

require_once('DAO.php');

/**
 * Esta clase se encarga del manejo de las acciones asociadas a los usuarios dentro del sistema
 * @author WEB: Proyectos Experimentales (DISE)
 */
class Usuario {
	
	var $username;		// Username que brinda el usuario al ingresar al CP
	var $userid;		// Valor aleatorio que se le asigna al usuario al momento de ingresar al sistema
	var $time;			// Tiempo (Fecha) de ultima actividad del usuario (pagina cargada)
	var $logged_in;		// Variable es verdadera cuando el usuario se ha logeado correctamente
	var $userinfo = array(); // Arreglo con toda la informacion del usuario
	
	
	function Usuario () {
		$this->time = time();
		$this->startSession();
		
		/* Valida si el usuario se ha logeado*/
		$this->logged_in = $this->checkLogin();
	}
	
	
	/**
    * startSession - Realiza todas las acciones necesarias para inicializar los objetos de la sesion.
	* Intenta determinar si el usuario ya se ha logeado e inicializa las variables con los valores
	* correspondientes.
    */
	function startSession(){
		global $app;
		session_start();
		
		/* Determina si el usuario se encuentra logeado */
		$this->logged_in = $this->checkLogin();
	}
	
	/**
	 * chechLogin - Verifica si el usuario se ya se encuentra logeado y se ha creado una sesion.
	 * Retorna verdadero si usuario se encuetra logeado
	 */
	function checkLogin(){
		global $app;
		if(isset($_SESSION['username']) && isset($_SESSION['userid']) && $_SESSION['username'] != ""){
			return true;
		}
		else{
			return false;
		}
	}
	
		
	
	/**
	 * login - Este metodo proceso el username y password ingresados por el usuario en el formulario de login
	 * de manera que determina si es un usuario valido o no. Si lo es, crea una sesion con la informacion
	 * correspondiente.
	 */
	function login($subuser, $subpass ){
		global $app;
		
		$datoOk = true;
		$subuser = trim($subuser);
		$subpass = trim($subpass);
		
				
		/* Checks that email is in database and password is correct */
		$subuser = stripslashes($subuser);
		$result = $this->validarUsuario($subuser, md5($subpass));
		
		/* Check error codes */
		if($result == 1){
			return false;
		}
		
		 /* Username and password correct, register session variables */
		 $this->userinfo  = $this->getUserInfo($subuser);
		 $this->username  = $_SESSION['username'] = $this->userinfo['email'];
		 $this->userid    = $_SESSION['userid']   = $this->generateRandID();
		 $this->userlevel = $this->userinfo['userlevel'];
		 
		 echo "<br />".$this->userinfo['email'];
		 echo "<br />".$_SESSION['username'];
		 echo "<br />".$_SESSION['userid'];

		 
		 
		if(isset($_SESSION['username']) && isset($_SESSION['userid']) && $_SESSION['username'] != ""){
			echo "Estan creadas las credenciales ";
			
		}
		else{
			echo "No se crearon credenciales";
		}
		
		 return true;

	}
	
	/**
	 * Verifica que el username y password proporcionados correspondan dentro de la base de datos.
	 * Si ocurre un error retorna 1, si se confirman las credenciales retorna 0
	 * @param $email
	 * @param $clave
	 * @return boolean, true o false en caso de que los datos sean correctos o no.
	 */
	function validarUsuario($email, $clave){
		
		
		/* Verify that user is in database */
		$q = "SELECT clave FROM usuario WHERE email = '$email'";
		$result = DAO::doSQL($q);
		
		
		if(!$result || (mysql_numrows($result) < 1)){
			return 1; //Email no valido o no encontrado
		}
		
		$dbarray = mysql_fetch_array($result);
		$dbarray['clave'] = stripslashes($dbarray['clave']);
		$clave = stripslashes($clave);
		
		if($clave == $dbarray['clave']){
			return 0; //Success! ERmail y clave confirmado
		}else{
			return 1; // Error en la clave. No coincide!
		}
	}
	
	/**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
	function getUserInfo($email){
		$q = "SELECT * FROM usuario WHERE email='$email'";
		$result = DAO::doSQL($q);
		/* Error occurred, return given name by default */
		if(!$result || (mysql_numrows($result) < 1)){
			return NULL;
		}
		
		
		/* Return result array */
		$dbarray = mysql_fetch_array($result);
		return $dbarray;
   }
   

	/**
    * generateRandID - Generates a string made up of randomized
    * letters (lower and upper case) and digits and returns
    * the md5 hash of it to be used as a userid.
    */
	function generateRandID(){
		return md5($this->generateRandStr(16));
	}
	
	/**
    * generateRandStr - Generates a string made up of randomized
    * letters (lower and upper case) and digits, the length
    * is a specified parameter.
    */
   function generateRandStr($length){
      $randstr = "";
      for($i=0; $i<$length; $i++){
         $randnum = mt_rand(0,61);
         if($randnum < 10){
            $randstr .= chr($randnum+48);
         }else if($randnum < 36){
            $randstr .= chr($randnum+55);
         }else{
            $randstr .= chr($randnum+61);
         }
      }
      return $randstr;
   }
	
	
	/**
    * logout - Gets called when the user wants to be logged out of the
    * website. It unsets session variables.
    */
   function logout(){
      global $app;
      
      /* Unset PHP session variables */
      unset($_SESSION['username']);
      unset($_SESSION['userid']);

      /* Reflect fact that user has logged out */
      $this->logged_in = false;
      $this->username  = "";

   }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public static function addUsser($email, $pass){
		
	}
	
	
	/**
	 * Este metodo se encarga de realizar la consulta para obtener todos los usuarios
	 * que se encuentran registrados en el sistema
	 * @return unknown_type
	 */
	public static function getAll(){
		return DAO::doSQLAndReturn( "SELECT * FROM usuario" );
		// TODO Validar si se desea obtener tambien los usuarios que no han validado su cuenta con el correo
	}
	
	
	/**
	 * Esta funcion se encarga de rotornar el usuario que posee una dirección
	 * @param unknown_type $email
	 * @return unknown_type
	 */
	public static function findByEmail($email) {
		$sql = 'SELECT * FROM usuario WHERE email = ' . DAO::escape_str($email);
		$r = DAO::doSQLAndReturn($sql);
		if (count($r) == 0) {
			return false;
		}
		return $r[0];
	}
	
}


/**
 * Inicializa el objeto de la sesion/Usuario
 */
 $usuario = new Usuario;

?>