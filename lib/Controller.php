<?php

//Controller.php (this is the front controller)
//this class is extended by the sub controllers (located in: ROOT . DS . 'apps/controllers/')
	
class Controller
{
    protected $model;
    protected $_view;

    public function __construct(ModelInterface $modelObj,$model, ViewInterface $viewObj)
    {
        //inject model object (using the $model variable)
	$this->$model = $modelObj;

        //inject view object
	$this->_view = $viewObj;
    }

   /**
    * Here is the workflow:
    * Input comes in from subview (from the page) > subcontroller (via set()) > Controller (via set()) > View,
    * and the View then extracts these variables and makes them available to the subview to
    * display (which is included as view_file.php).
    */
    public function set($name,$value)
    {
        $this->_view->set($name,$value);
    }

    public function __destruct()
    {
        echo '<br />Controller has been destructed<br />';
        
        //this extracts the variables set by 'set()' above, and then
        //includes the the subview (view_file.php).
        $this->_view->render();
    }
}
