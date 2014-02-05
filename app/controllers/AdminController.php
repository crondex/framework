<?php

//AdminController.php

class AdminController extends Controller
{
    public function index()
    {
        //$this->set('title','Admin Home:');
    }

    public function login()
    {
        //prevent caching of login credentials
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	$user = $_POST['user'];
	$pass = $_POST['pass'];
        $this->set('title','Login:');
        $this->set('auth',$this->AdminModel->loginUser($user, $pass)); //this calls the model method
        //forward away, this prevents logging-in by click back and then reloading the login page (after a logout)
//        header("Location: ../");
    }

    public function logout()
    {
        $this->set('title','Logout:');
        $this->set('auth',$this->AdminModel->logout());
//        header("Location: ../");
    }

    public function newuser()
    {
	$user = $_POST['user'];
	$pass = $_POST['pass'];
        $this->set('title','Create New User:');
        $this->set('auth',$this->AdminModel->createNewUser($user, $pass));
    }

    public function passwd()
    {
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$newpass = $_POST['newpass'];
        $this->set('title','Change Password:');
        $this->set('auth',$this->AdminModel->changePass($user, $pass, $newpass));
    }

    //if this is unccommented, the parent destructor must be called explicitly.
    //function __destruct()
    //{
        //parent::__destruct()
    //}
}

