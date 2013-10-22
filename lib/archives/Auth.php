<?php

//Auth.php

require './PasswordHash.php';
require './config.php';
require './Database.php';

//this can go in the config
/* Dummy salt to waste CPU time on when a non-existent username is requested.
 * This should use the same hash type and cost parameter as we're using for
 * real/new hashes.  The intent is to mitigate timing attacks (probing for
 * valid usernames).  This is optional - the line may be commented out if you
 * don't care about timing attacks enough to spend CPU time on mitigating them
 * or if you can't easily determine what salt string would be appropriate. */
$dummy_salt = '$2a$08$1234567890123456789012';


//I need to create a class, or update my router class to manage post vars
function get_post_var($var) {
	$val = $_POST[$var];
	if (get_magic_quotes_gpc())
		$val = stripslashes($val);
	return $val;
}

$op = $_POST['op'];

//post vars need to be handled by the router class, or possibly another another post_var class
$user = get_post_var('user');
/* Sanity-check the username, don't rely on our use of prepared statements
 * alone to prevent attacks on the SQL server via malicious usernames. */
if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user))
	$msg->fail('Invalid username');

$pass = get_post_var('pass');


//this stuff can go in the config file
// Base-2 logarithm of the iteration count used for password stretching
$hash_cost_log2 = 8;
// Do we require the hashes to be portable to older systems (less secure)?
$hash_portable = FALSE;

$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

$db = new Database($config);
$msg = new Msg;
$msg->debug = TRUE;
$auth = new Auth($msg, $hasher);


//this will eventually be controlled by the controller

if ($op === 'createNewUser') {

	if($auth->createNewUser($user, $pass, $hasher, $db, $msg))
		echo "New user $user was created.";

} elseif ($op === 'loginUser') {

	echo "loginUser<br />";
	$auth->loginUser($user, $pass, $hasher, $db, $msg, $dummy_salt);

} elseif ($op === 'changePassword') {

	echo "changePassword";
	$auth->changePassword();
}




// For now, classes defined below

class Msg {

	public $pubMsg, $pvtMsg;

	public function __construct() {
		$this->debug = FALSE;
	}
	
	public function fail($pubMsg, $pvtMsg = '') {

		$this->message = $pubMsg;

		//debugging debug
		//if ($this->debug) {
		//	var_dump($this->debug);
		//}

		if ($this->debug && $pvtMsg !== '') {
			$this->message .= ": $pvtMsg";
		}
		echo 'An error occured' . $this->message . '<br />';
	}
}


class Auth {
	
	public $user, $pass, $db, $newpass, $hasher, $msg, $dummy_salt;

	public function passCheck($pass, $msg) {
		if (strlen($pass) < 4) {
			$msg->fail(": Password is too short.");
			return false;
		} elseif (strlen($pass) > 72) {
			$msg->fail(": Password is too long.");
			return false;
		}
		return true;
	}

	public function createNewUser($user, $pass, $hasher, $db, $msg) {

		//hash password
		if ($this->passCheck($pass, $msg)) {
			$hash = $hasher->HashPassword($pass);
		}

		if (isset($hash) && strlen($hash) > 20) {

			$sql = "INSERT INTO users (username, password) VALUES (?, ?)";
			$params = array($user, $hash);

			//query the database	
			if ($db->query($sql, $params)) {

				return true;	

			//check why it failed
			} else {

				//username taken
				if ($db->sqlErrorCode === 1062 ) {
					echo "Username already taken";
				}
				return false;
			}

		//failed to hash
		} else {
			$msg->fail('', "Failed to hash password.");
			return false;
		}

		return true;
	}


	public function loginUser($user, $pass, $hasher, $db, $msg, $dummy_salt) {

		if ($this->passCheck($pass, $msg)) {	

			
			//query the db for the user
			$sql = "SELECT password FROM users where username=?";
			$params = array($user);

			//grab the hash from the user's row
			$rows = $db->query($sql, $params, 'names');

			var_dump($rows);

			//was the query successful
			if ($rows) {

				//loop through each row (there should only be one match)
				foreach ($rows as $row) {
					$hash = $row['password'];
				}

			} else {

				echo "test";
				//var_dump($db->sqlErrorCode);
				//$msg->fail('', $db->sqlErrorCode);
			}

			//if the hash wasn't in the db
			if (isset($dummy_salt) && (!isset($hash) || strlen($hash) < 20 )) {

				//Mitigate timing attacks (attackers probing for valid usernames)
				$hash = $dummy_salt;
			}
		
			//echo "<p>";
			//echo "pass: $user<br />";
			//echo "pass: $pass<br />";
			//echo "hash: $hash<br />";
			//echo "</p>";

			if ($hasher->CheckPassword($pass, $hash)) {
				echo 'Authentication succeeded';
				return true;
			} else {
				$msg->fail(': Authentication failed');
				return false;
			}
		}
	}

/*

	public function changePassword() {

		$this->loginUser();

		$newpass = get_post_var('newpass');

		if (strlen($newpass) > 72)
			$msg->fail('The new password is too long');
		if (($check = pwcheck($newpass, $pass, $user)) !== 'OK')
			fail($check);

		$hash = $hasher->HashPassword($newpass);

		if (strlen($hash) < 20)
			$msg->fail('Failed to hash new password');

		($stmt = $db->prepare('update users set pass=? where user=?'))
			|| $msg->fail('MySQL prepare', $db->error);
		$stmt->bind_param('ss', $hash, $user)
			|| $msg->fail('MySQL bind_param', $db->error);
		$stmt->execute()
			|| $msg->fail('MySQL execute', $db->error);

		$this->what = 'Password changed';		

	}

	public function isAdmin() {
		//$selection being the array of the row returned from the database.
		if($selection['is_admin'] == 1) {
			return true;
		}
			
		return false;
	}
*/
}

