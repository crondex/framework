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
	$user = $_POST['user'];
	$pass = $_POST['pass'];
        $this->set('title','Login:');
        $this->set('auth',$this->AdminModel->loginUser($user, $pass)); //this calls the model method
    }

    public function logout()
    {
        $this->set('title','Logout:');
        $this->set('auth',$this->AdminModel->logout());
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

