<?php

use Crondex\Model\Model;
use Crondex\Security\RandomInterface;
use Crondex\Auth\AuthInterface;
use Crondex\Log\MsgInterface;

class AdminModel extends Model
{
    public $config;
    //protected $_dummy_salt;
    protected $_auth;
    protected $_msg;

    function __construct($config, RandomInterface $randomObj, AuthInterface $authObj, MsgInterface $msgObj)
    {
        //call the parent constructor
	parent::__construct($config);

        //inject object
        $this->_random = $randomObj;

        //inject object
        $this->_auth = $authObj;

        //inject object
        $this->_msg = $msgObj;
    }

    //checks for valid username
    public function userCheck($user)
    {
        //Sanity-check the username, don't rely on our use of prepared statements
        //alone to prevent attacks on the SQL server via malicious usernames.
        if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user)) {
            $this->_msg->fail(': Invalid username');
            return false;
	}
	return true;
    }

    //checks password length
    public function passCheck($pass)
    {
        if (!isset($pass) || strlen($pass) < 4) {
            $this->_msg->fail(': Password is too short');
            return false;
        } elseif (strlen($pass) > 72) {
            $this->_msg->fail(': Password is too long');       
            return false;
        }
	return true;
    }

    public function hash($pass)
    {
        //use phpass hasher to has password
        $this->_hash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 10));

        if (isset($this->_hash) && strlen($this->_hash) > 20) {
            return true;
        } else {
            return false;
	}
    }

    public function authenticate($user, $pass)
    {
        if (($this->userCheck($user)) && ($this->passCheck($pass))) {

            //set prepared statements
            $sql = "SELECT password FROM $this->_table where username=?";
            $params = array($user);

            //grab the hash from the user's row
            $rows = $this->query($sql, $params, 'names');

            //was the query successful
            if ($rows) {

                //loop through each row (there should only be one match)
                foreach ($rows as $row) {
                    $hash = $row['password'];
                }
            } else {
                return false;
            } 

            //check password
            if (password_verify($pass, $hash)) {
                $this->_msg->success('Authentication Succeeded!');
                return true;
            } else {
                $this->_msg->fail(': Bad username/password combination.');
                return false;
            }   
           return false;
        } 
        return false;
    }

    public function loginUser($user, $pass)
    {
        if ($this->authenticate($user, $pass)) {
            //login
            $this->_auth->login($user);

        } else {
            //logout
            $this->_auth->logout();
        }
        
        return $this->_msg->getMessage();
    }

    public function changePass($user, $pass, $newpass)
    {
        if($this->authenticate($user, $pass)) {

            //if $user, $pass, $newpass all checkout, and if $newpass hashes okay:
            if ($this->userCheck($user) && $this->passCheck($pass) && $this->passCheck($newpass) && $this->hash($newpass)) {
               
                //set prepared statements
                $sql = "UPDATE $this->_table SET password=? WHERE username=?";
                $params = array($this->_hash, $user);

                //query the database    
                if ($this->query($sql, $params)) {

                    //user was created successfully
                    $this->_msg->success("Your password has been changed to \"$newpass\".");

                } else {
                    //it must have failed for some other reason
                    $this->_msg->fail(': Password change failed.');
                } 
            }
        }
        return $this->_msg->getMessage();
    }

    public function createNewUser($user, $pass)
    {
        //check username and password
        if ($this->userCheck($user) && $this->passCheck($pass)) {

            //if hashing was successful (this also sets the hash)
            if ($this->hash($pass)) {

                //set prepared statements
                $sql = "INSERT INTO $this->_table (username, password) VALUES (?, ?)";
                $params = array($user, $this->_hash);

                //query the database    
                if ($this->query($sql, $params)) {

                    //user was created successfully
		    $this->_msg->success("The user \"$user\" Created.");

                //check why the query fail?
                } else {

                    //username taken
                    if ($this->sqlErrorCode == 1062 ) {
                        $this->_msg->fail(': Username already taken');
                    } else {
                        $this->_msg->fail(': User creation failed.');
                    }
                }
            }
        }

        //return the status
        return $this->_msg->getMessage();
    }

    public function logoutUser() {

        //set username
        isset($_SESSION['username']) ? $username = $_SESSION['username'] : $username = 'User';

        //logout
        if($this->_auth->logout()) {
            $this->_msg->success("$username has been logged out.");
        } else {
            $this->_msg->fail("$username has been logged out.");
        }
        return $this->_msg->getMessage();
    }
}

