<?php namespace Crondex\Auth;

use Crondex\Model\Model;
use Crondex\Security\Random;
use Crondex\Security\RandomInterface;

class Auth extends Model implements AuthInterface
{
    protected $_random;
    protected $_user;
    protected $_token;

    public function __construct($config, RandomInterface $randomObj)
    {
        //call the parent constructor
        parent::__construct($config);

        $this->_random = $randomObj;
    }

    protected function get_token() {

        /*
         * Assign a random value to $token
         */
        $token = $this->_random->get_random_bytes(50);

        /*
         * hash the token
         * although not a password, we're using the password_hash function
         */
        $this->_token = password_hash($token, PASSWORD_BCRYPT, array("cost" => 5));

        if (isset($token)) {
            return true;
        }
        return false;
    }

    protected function refresh($user_id)
    {
        //Regenerate id
	session_regenerate_id();

        //setup session
        if ($this->get_token()) {
            $_SESSION['token'] = $this->_token;

            //set sql to update token logged-in-users
            $sql = "UPDATE logged_in_users SET session_id=?, token=? WHERE user_id=?";
            $params = array(session_id(), $this->_token, $user_id);

            //update database
            if ($this->query($sql, $params, 'names')) {
                //echo "session has been updated";
                return true;
            }
            return false;
            //echo "session update failed";
        }
        return false;
        //echo "session update failed - new token not set";
    }

    public function removeLoggedInUser() {

        //if $_SESSION variables are set
        if (isset($_SESSION['user_id']) || isset($_SESSION['token'])) {

            //delete old 'logged_in_user' record
            $sql = "DELETE FROM logged_in_users WHERE user_id=? OR session_id=? OR token=?";
            $params = array($_SESSION['user_id'], session_id(), $_SESSION['token']);
        
            if ($this->query($sql, $params, 'names')) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function login($user) {

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
        } else {
            return false;
        }

        //setup session vars
        if ($this->get_token()) {
            $_SESSION['token'] = $this->_token;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $user;
        } else {
            return false;
        }

        //first remove logged-in users
        if ($this->removeLoggedInUser()) {

            //next insert new 'logged_in_user' record
            $sql = "INSERT INTO logged_in_users (user_id, session_id, token) VALUES (?, ?, ?)";
            $params = array($user_id, session_id(), $this->_token);

            //grab the hash from the user's row
            if ($this->query($sql, $params, 'names')) {
	        return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function check()
    {
        if (isset($_SESSION['user_id'])) {

            $sql = "SELECT * FROM logged_in_users WHERE user_id=?";
            $params = array($_SESSION['user_id']);
            $rows = $this->query($sql, $params, 'names');

            if ($rows) {

                //loop through each row (there should only be one match)
                foreach ($rows as $row) {

                    $session_id = $row['session_id'];
                    $token = $row['token'];
                }

                //check to see if the session_id and token match the database
                if ($session_id === session_id() && $token === $_SESSION['token']) {
                    $this->refresh($_SESSION['user_id']);
                    //echo "THEY ARE THE SAME!";
                } else {
                    //echo "THEY ARE DIFFERENT!";
                    $this->logout();
                }
            }
        }
    }

    public function logout()
    {
        if ($this->removeLoggedInUser()) {

            session_unset();
            $_SESSION = '';
            session_destroy();

            return true;
        }
        return false;
    }
}

