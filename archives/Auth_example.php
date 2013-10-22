<?php

class Auth {
	private $_siteKey;

	public function __construct() {
		$this->siteKey = 'my site key will go here';
	}

	private function randomString($length = 50) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';    
		
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters))];
		}
		
		return $string;
	}

	protected function hashData($data)
    	{
		return hash_hmac('sha512', $data, $this->_siteKey);
	}

	public function isAdmin() {		
		//$selection being the array of the row returned from the database.
		if($selection['is_admin'] == 1) {
			return true;
		}	
		return false;
	}

	public function createUser($email, $password, $is_admin = 0) {			

		//Generate users salt
		$user_salt = $this->randomString();
				
		//Salt and Hash the password
		$password = $user_salt . $password;
		$password = $this->hashData($password);
				
		//Create verification code
		$code = $this->randomString();

		//Commit values to database here.
		$created = …

		if($created != false){
			return true;
		}
				
		return false;
	}

	public function createUser($email, $password, $is_admin = 0) {			

		//Generate users salt
		$user_salt = $this->randomString();
				
		//Salt and Hash the password
		$password = $user_salt . $password;
		$password = $this->hashData($password);
				
		//Create verification code
		$code = $this->randomString();

		//Commit values to database here.
		$created = …

		if($created != false){
			return true;
		}
				
		return false;
	}

	//First, generate a random string.
	$random = $this->randomString();
	       //Build the token
	$token = $_SERVER['HTTP_USER_AGENT'] . $random;
	$token = $this->hashData($token);



}





?>
