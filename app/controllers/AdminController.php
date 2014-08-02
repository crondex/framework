<?php

use Crondex\Routing\Controller;

class AdminController extends Controller
{
    public function index()
    {
        $this->set('title','Admin Home:');
        $this->model->test();
    }

    public function login()
    {
        $this->model->env->setHeaders('noCache');
	$user = $this->getPost('user');
	$pass = $this->getPost('pass');
        $this->set('title','Login:');
        $this->set('auth',$this->model->loginUser($user, $pass)); //this calls the model method

        //forward away, this prevents logging-in by click back and then reloading the login page (after a logout)
        header("Location: ../");
    }

    public function logout()
    {
        $this->set('title','Logout:');
        $this->set('auth',$this->model->logoutUser());
        header("Location: ../");
    }

    public function newuser()
    {
	$user = $this->getPost('user');
	$pass = $this->getPost('pass'); 
        $this->set('title','Create New User:');
        $this->set('auth',$this->model->createNewUser($user, $pass));
    }

    public function passwd()
    {
	$user = $this->getPost('user');
	$pass = $this->getPost('pass'); 
	$newpass = $this->getPost('newpass'); 
        $this->set('title','Change Password:');
        $this->set('auth',$this->model->changePass($user, $pass, $newpass));
    }
}

