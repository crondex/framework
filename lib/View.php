<?php

//View.php (this is the main view/template)

class View
{
    protected $_view;
    protected $_action;
    protected $variables = array();

    public function __construct($view, $action)
    {
        $this->_view = $view;
	$this->_action = $action;
    }

    public function set($name,$value)
    {
        $this->variables[$name] = $value;
    }

    public function render()
    {
        extract($this->variables);

	//set view file
        $view_file = ROOT . DS . 'app' . DS . 'views' . DS . $this->_view . DS . $this->_action . '.php';

        //load view file
        if (file_exists($view_file)) {
            include ($view_file);
        } else {
            echo "404 will be included here.";
	}

        //include (ROOT . DS . 'app' . DS . 'views' . DS . $this->_view . DS . $this->_action . '.php');      
    }

    public function __destruct()
    {
        echo '<br />View has been destructed<br />';
    }
}
