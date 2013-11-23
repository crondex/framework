<?php

//ParseUrl.php

class ParseUrl
{
    protected $_url;
    protected $_urlArray = array();
    protected $_controller;
    protected $_model;
    protected $_view;
    protected $_action;
    protected $_queryArray = array();
    protected $_queryString;

    public function __construct($url)
    {
        $this->_url = $url;

        //check if $url ends in /
        if (substr($this->_url, -1) != '/') { $this->_url .= '/'; }

        //break out $url into an array, delimited by '/'
        $this->_urlArray = explode('/',$this->_url);
    }

/*
    public function routeUrl($url, $routes)
    {
	foreach ( $routing as $pattern => $result ) {
            if ( preg_match( $pattern, $url ) ) {
				return preg_replace( $pattern, $result, $url );
			}
	}
	return ($url);
    }
*/

    public function getController()
    {
        if (isset($this->_urlArray[0]) && !empty($this->_urlArray[0])) {
            $this->_controller = ucfirst(strtolower($this->_urlArray[0]));
	} else {
            $this->_controller = 'Index';
        }
        $this->_controller .= 'Controller';
        return $this->_controller;
    }

    public function getModel()
    {
        if ($this->getController()) {
            $this->_model = str_replace('Controller','Model',$this->getController()); 
	    return $this->_model;
        }
    }

    public function getView()
    {
        if ($this->getController()) {
            $this->_view = strtolower(str_replace('Controller','',$this->getController())); 
            return $this->_view;
        }
    }

    public function getAction()
    {
        //if action is set and not empty (it would be empty if the user types a '/' of the url
        if (isset($this->_urlArray[1]) && !empty($this->_urlArray[1])) { 
            $this->_action = strtolower($this->_urlArray[1]);
	} else {
	    //if not set, set to default
            $this->_action = 'index';
        }
        return $this->_action;
    }

    public function getQueryString()
    {
        if (isset($this->_urlArray[2]) && $this->_urlArray[2] != '') {

            //We don't need the controller or the action (the first two elements)
            //get everything except for the first two elements of $this->_urlArray
            $this->_queryArray = array_slice($this->_urlArray, 2);

            //reconstruct path
            foreach ($this->_queryArray as $value) {
		$this->_queryString .= DS . $value;
            }
	    return $this->_queryString;
	}
    }
}

