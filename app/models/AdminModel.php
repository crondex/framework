<?php

use Crondex\Model\Model;
use Crondex\Security\RandomInterface;
use Crondex\Auth\AuthInterface;
use Crondex\Helpers\MsgInterface;
use Crondex\Config\EnvironmentInterface;

class AdminModel extends Model
{
    public $config;
    public $env;
    protected $auth;
    protected $msg;

    function __construct($config, RandomInterface $randomObj, AuthInterface $authObj, MsgInterface $msgObj, EnvironmentInterface $envObj)
    {
        //call the parent constructor
	parent::__construct($config, $authObj);

        //inject objects
        $this->random = $randomObj;
        $this->auth = $authObj;
        $this->msg = $msgObj;
        $this->env = $envObj;
        $this->config = $config;

        $this->usersTable = $this->config->get('usersTable');

        //set no caching
        //$this->env->setHeaders('noCache');
    }

    //checks for valid username
    public function userCheck($user)
    {
        //Sanity-check the username, don't rely on our use of prepared statements
        //alone to prevent attacks on the SQL server via malicious usernames.
        if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user)) {
            $this->msg->fail(': Invalid username');
            return false;
	}
	return true;
    }

    //checks password length
    public function passCheck($pass)
    {
        if (!isset($pass) || strlen($pass) < 4) {
            $this->msg->fail(': Password is too short');
            return false;
        } elseif (strlen($pass) > 72) {
            $this->msg->fail(': Password is too long');       
            return false;
        }
	return true;
    }

    public function hash($pass)
    {
        //use phpass hasher to has password
        $this->hash = password_hash($pass, PASSWORD_BCRYPT, array("cost" => 10));

        if (isset($this->hash) && strlen($this->hash) > 20) {
            return true;
        } else {
            return false;
	}
    }

    public function authenticate($user = '', $pass = '')
    {
        if (($this->userCheck($user)) && ($this->passCheck($pass))) {

            //set prepared statements
            //$sql = "SELECT password FROM $this->table where username=?";
            $sql = "SELECT password FROM $this->usersTable where username=?";
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
                $this->msg->success('Authentication Succeeded!');
                return true;
            } else {
                $this->msg->fail(': Bad username/password combination.');
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
            $this->auth->login($user);

        } else {
            //logout
            $this->auth->logout();
        }
        
        return $this->msg->getMessage();
    }

    public function changePass($user, $pass, $newpass)
    {
        if($this->authenticate($user, $pass)) {

            //if $user, $pass, $newpass all checkout, and if $newpass hashes okay:
            if ($this->userCheck($user) && $this->passCheck($pass) && $this->passCheck($newpass) && $this->hash($newpass)) {
               
                //set prepared statements
                //$sql = "UPDATE $this->table SET password=? WHERE username=?";
                $sql = "UPDATE $this->usersTable SET password=? WHERE username=?";
                $params = array($this->hash, $user);

                //query the database    
                if ($this->query($sql, $params)) {

                    //user was created successfully
                    $this->msg->success("Your password has been changed to \"$newpass\".");

                } else {
                    //it must have failed for some other reason
                    $this->msg->fail(': Password change failed.');
                } 
            }
        }
        return $this->msg->getMessage();
    }

    public function createNewUser($user, $pass)
    {
        //check username and password
        if ($this->userCheck($user) && $this->passCheck($pass)) {

            //if hashing was successful (this also sets the hash)
            if ($this->hash($pass)) {

                //set prepared statements
                //$sql = "INSERT INTO $this->table (username, password) VALUES (?, ?)";
                $sql = "INSERT INTO $this->usersTable (username, password) VALUES (?, ?)";

                $params = array($user, $this->hash);

                //query the database    
                if ($this->query($sql, $params)) {

                    //user was created successfully
		    $this->msg->success("The user \"$user\" Created.");

                //check why the query fail?
                } else {

                    //username taken
                    if ($this->sqlErrorCode == 1062 ) {
                        $this->msg->fail(': Username already taken');
                    } else {
                        $this->msg->fail(': User creation failed.');
                    }
                }
            }
        }

        //return the status
        return $this->msg->getMessage();
    }

    public function test() {
        if (isset($this->auth->username)) {
            var_dump($this->auth->username);
        }
    }

    public function logoutUser() {
        $this->auth->logout();
    }
}

