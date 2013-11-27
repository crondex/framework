<?php

class Sessions extends Model implements SessionsInterface
{
    protected $_hasher;
    protected $_user;

    public function __construct($config, HasherInterface $hasherObj)
    {
        //call the parent constructor
        parent::__construct($config);

        $this->_hasher = $hasherObj;
    }

    public function removeLoggedInUser() {

        //open session
        session_start(); //possibly check to see if it's started before running this

        //delete old 'logged_in_user' record
        $sql = "DELETE FROM logged_in_users WHERE user_id=? OR session_id=? OR token=?";
        $params = array($_SESSION['user_id'], session_id(), $_SESSION['token']);
        
        if ($this->query($sql, $params, 'names')) {
            return true;
        } else {
            return false;
        }
    }

    public function start($user) {

        //grab user row based on username
        $sql = "SELECT * FROM admin where username=?";
        $params = array($user);
        $rows = $this->query($sql, $params, 'names');

        //get user's 'id' and assign to $user_id
        if ($rows) {
            //loop through each row (there should only be one match)
            foreach ($rows as $row) {
                $user_id = $row['id'];
            }
        }

        //First, generate a random string.
        $random = $this->_hasher->get_random_bytes(50);

        //Build the token
        $token = $_SERVER['HTTP_USER_AGENT'] . $random;

        //hash the token
        //although not a password, we're using the HashPassword method
        $token = $this->_hasher->HashPassword($token);

        //setup session
        session_start();
        $_SESSION['token'] = $token;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $user;

        //functional testing
        print_r($token);
        echo '<h1>' . strlen($token) . '</h1>';

        //delete old 'logged_in_user' record
//        $sql = "DELETE FROM logged_in_users WHERE user_id=?";
//        $params = array($user_id);
//        $this->query($sql, $params, 'names');

        //remove logged-in User
        $this->removeLoggedInUser();

        //insert new 'logged_in_user' record
        $sql = "INSERT INTO logged_in_users (user_id, session_id, token) VALUES (?, ?, ?)";
        $params = array($user_id, session_id(), $token);

        //grab the hash from the user's row
        if ($this->query($sql, $params, 'names')) {
	    echo "SUCCESS!";
        } else {
	    echo "FAIL!";
        }
    }

    public function end()
    {
        if ($this->removeLoggedInUser()) {
            session_unset();
            session_destroy();
            return true;
        } else {
            return false;
        }
    }
}
