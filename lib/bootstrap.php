<?php

//bootstrap.php

//temporarily set $_POST
//$_POST['fruit'] = 'kiwi';
//$_POST['vegitable'] = 'carrot';

setReporting();
removeMagicQuotes();
unregisterGlobals();

try {

    echo "\$url pre parse: $url";

    $parseUrl = new ParseUrl($url);
//    $db = new Database($config);  //This is not needed, as model extends this class and is instantiated below.

    echo '<br />controller: ' . $parseUrl->getController();
    echo '<br />action: ' . $parseUrl->getAction();
    echo '<br />queryString: ' . $parseUrl->getQueryString();

    $model = $parseUrl->getModel();
    $controller = $parseUrl->getController();
    $action = $parseUrl->getAction();
    $view = $parseUrl->getView();
    $queryString = $parseUrl->getQueryString();

    //set hasher vars
    $hash_cost_log2 = $config['hash_cost_log2'];
    $hash_portable = $config['hash_portable'];
    $dummy_salt = $config['dummy_salt'];

    //instantiate hasher and msg
    $hasherObj = new PasswordHash($hash_cost_log2, $hash_portable);
    $msgObj = new Msg;
    $msgObj->debug = TRUE;

    //instantiate model
    $modelObj = new $model($config,$hasherObj,$msgObj);

    //instantiate view (template)
    $viewObj = new View($view,$action);

   /**
    * This instantiates the dispatch object (which is an instance
    * of $controller (the subcontroller), which extends the
    * Controller (the main/front controller) also inject model
    * and view objects
    */
    $dispatch = new $controller($modelObj,$model,$viewObj);

    //Checks to see if method $action (in class $controller) exists
    if ((int)method_exists($controller, $action)) {

       /**
        * This calls the method $action of the object
        * $dispatch (the subcontroller) and passes it the
        * argument $queryString
        * 
        * Uncomment next line if I decide to use this instead of echo
        * call_user_func_array(array($dispatch,$action),$queryString);
        *
        */

        //I think this does the same thing as call_user_func_array
        echo $dispatch->$action($queryString);

    } else {
        throw new Exception('404');	
    } 

} catch (Exception $Exception) {

    if ($Exception->getMessage() === '404') {
        throw404();
    }
}

