<?php

//Controller.php (this is the front controller)
//this class is extended by the sub controllers (located in: ROOT . DS . 'apps/controllers/')
	
class Controller
{
    protected $model;
    protected $_view;

    public function __construct(Model $modelObj,$model, View $viewObj)
    {
        //inject model object (using the $mode variable)
	$this->$model = $modelObj;

        //inject view object
	$this->_view = $viewObj;
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
