<?php

//bootstrap.php

//temporarily set $_POST
$_POST['fruit'] = 'apple';
$_POST['vegitable'] = 'carrot';

//temporarily set
$authenticated = TRUE;


class ParseUrl {

    private $_url;
    private $_urlArray = array();
    private $_controller;
    private $_action;
    private $_queryArray = array();
    private $_queryString;
    private $_model;

    public function __construct($url) {
        $this->_url = $url;
        $this->_urlArray = explode("/",$this->_url);
    }

    public function getController() {
        if (isset($this->_urlArray[0])) {
            $this->_controller = ucfirst(strtolower($this->_urlArray[0])) . 'Controller';
            return $this->_controller;
	}
    }

    public function getAction() {
         if (isset($this->_urlArray[1])) {
            $this->_action = strtolower($this->_urlArray[1]);
            return $this->_action;
	} 
    }

    public function getQueryString() {
        if (isset($this->_urlArray[2])) {

            //get everything except for the first two elements of $this->_urlArray
            $this->_queryArray = array_slice($this->_urlArray, 2);

            //reconstruct path
            foreach ($this->_queryArray as $value) {
		$this->_queryString .= '/' . $value;
            }
	    return $this->_queryString;
	}
    }

    public function getModel() {
        if (isset($this->_urlArray[0])) {        
            $this->_model = str_replace('Controller','Model',$this->getController()); 
	    return $this->_model;
        }
    }

}

//this should probably be the main controller
if ($authenticated) {
    //do stuff
    try {
	//debugging
        $parseUrl = new ParseUrl($url);
        echo 'controller: ' . $parseUrl->getController() . '<br />';             //debugging
        echo 'action: ' . $parseUrl->getAction() . '<br />';                       //debugging
        echo 'model: ' . $parseUrl->getModel() . '<br />';                         //debugging
        echo 'queryString: ' . $parseUrl->getQueryString() . '<br />';           //debugging

        $controller = $parseUrl->getController();
	$model = $parseUrl->getModel();
        $action = $parseUrl->getAction();
	$queryString = $parseUrl->getQueryString();

        $db = new Database($config);
        print_r($_GET); //DEBUGGING

        $controller = new $controller;
	//$controller = new Controller;

        $controller->$action();
        //$controller->doUserAction($db);

    } catch (Exception $Exception) {
        echo 'Oops! ' . $Exception->getMessage();
    }
} else {
    echo "Please login:";
    echo "Username:<br />";
    echo "Password:<br />";
    echo "Don't have a login? Create an account.";
}

