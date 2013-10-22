<?php

//Controller.php (this is the front controller)
//this class is extended by the sub controllers (located in: ROOT . DS . 'apps/controllers/')
	
class Controller
{
    protected $_model;
    protected $_controller;
    protected $_action;
    protected $_view;

    public function __construct($model,$config,$controller,$action,$view)
    {
        $this->_model = $model;
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = $view;

	//instantiate model
	$this->$model = new $this->_model($config);

        //instantiate view (template)
	$this->_view = new View($view,$action);
    }

    //this is called by the sub-controllers
    public function set($name,$value)
    {
        $this->_view->set($name,$value);
    }

    public function __destruct()
    {
        echo '<br />Controller has been destructed<br />';
        $this->_view->render();
    }
}

