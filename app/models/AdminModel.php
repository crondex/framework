<?php

//AdminModel.php

class AdminModel extends Model
{
    protected $_hasher;
    protected $_msg;

    function __construct($config, HasherInterface $hasherObj, MsgInterface $msgObj)
    {
        //call the parent constructor
	parent::__construct($config);

        //inject object
        $this->_hasher = $hasherObj;

        //inject object
        $this->_msg = $msgObj;

	//echo 'Random number:' . $this->_hasher->get_random_bytes(50);
    }

    public function userCheck($user)
    {
        /* Sanity-check the username, don't rely on our use of prepared statements
        * alone to prevent attacks on the SQL server via malicious usernames. */
        if (!preg_match('/^[a-zA-Z0-9_]{1,60}$/', $user)) {
            $this->_msg->fail(': Invalid username');
            return false;
	}
	return true;
    }

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
            }

            //if the hash wasn't in the db
            if (isset($this->_dummy_salt) && (!isset($hash) || strlen($hash) < 20 )) {

                //Mitigate against timing attacks (attackers probing for valid usernames)
                $hash = $this->_dummy_salt;
            }

            if ($this->_hasher->CheckPassword($pass, $hash)) {
                $this->_msg->success('Authentication Succeeded!');
                return true;
            } else {
                $this->_msg->fail(': Bad username/password combination.');
                return false;
            }   
	} 
        return true;
    }

    public function loginUser($user, $pass)
    {
        $this->authenticate($user, $pass);
        return $this->_msg->getMessage();
    }

    public function hash($pass)
    {
        $this->_hash = $this->_hasher->HashPassword($pass);

        if (isset($this->_hash) && strlen($this->_hash) > 20) {
            return true;
        } else {
            return false;
	}
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

            //if hashing was successful (this also sets the hash
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
}

