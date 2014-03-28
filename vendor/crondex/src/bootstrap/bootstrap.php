<?php

use Crondex\Config\Config;
use Crondex\View\View;
use Crondex\Security\Random;
use Crondex\Routing\ParseUrl;
use Crondex\Session\SessionManager;
use Crondex\Auth\Auth;
use Crondex\Log\Msg;
use Crondex\Html\Clean;

setReporting();
removeMagicQuotes();
unregisterGlobals();
noCache();

//print headers
//foreach (getallheaders() as $name => $value) {
//    echo "$name: $value\n";
//}

try {

    //inject the config path when I make this a class;
    $config = new Config(ROOT . DS . 'app' . DS . 'config' . DS . 'main.php');

    $parseUrl = new ParseUrl($config->get('url'));
    $model = $parseUrl->getModel();
    $controller = $parseUrl->getController();
    $action = $parseUrl->getAction();
    $view = $parseUrl->getView();
    $queryString = $parseUrl->getQueryString();

    //set hasher vars
    //$hash_cost_log2 = $config->get('hash_cost_log2');
    //$hash_portable = $config->get('hash_portable');

    //dummy salt
    //$dummy_salt = $config->get('dummy_salt'); //delete this soon

    //instantiate hasher
    //$hasherObj = new PasswordHash($hash_cost_log2, $hash_portable); //this is PHPass
    $randomObj = new Random; //this is from PHPass

    //instantiate session handler
    $sessionManager = new SessionManager($config);
    session_set_save_handler($sessionManager);

    //instatiate auth manager and check auth/session
    $authObj = new Auth($config,$randomObj);
    $authObj->check();

    //debugging
    $msgObj = new Msg;
    $msgObj->debug = $config->get('msg_debug');

    //instantiate model
    $modelObj = new $model($config,$randomObj,$authObj,$msgObj);

    //instantiate view (template)
    $viewObj = new View($view,$action);

    //instantiate input cleaner
    $clean = new Clean;

    /**
     * This instantiates the dispatch object (which is an instance
     * of $controller (the subcontroller), which extends
     * Controller (the main/front controller) ans also injects model
     * and view objects
     */
    $dispatch = new $controller($modelObj,$model,$viewObj);

    /**
     * Checks to see if method $action (in class $controller) exists
     */
    if ((int)method_exists($controller, $action)) {

        /*
         * This calls the method $action (with parameter $queryString) of the 
         * object object $dispatch (which was instatiated above).
         *
         * Another way to do this is to use 'call_user_func_array'. See below.
         */
         return $dispatch->$action($queryString);

        /**
         *
         * This calls the method $action of the object
         * $dispatch (the subcontroller) and passes it the
         * argument $queryString
         * 
         * Uncomment next line if I decide to use this instead of 'return' (below)
         * call_user_func_array(array($dispatch,$action),$queryString);
         *
         */


    } else {
        throw new Exception('404');	
    } 

} catch (Exception $Exception) {

    if ($Exception->getMessage() === '404') {
        throw404();
    }
}

