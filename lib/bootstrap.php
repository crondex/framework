<?php

//bootstrap.php

//temporarily set $_POST
$_POST['fruit'] = 'kiwi';
$_POST['vegitable'] = 'carrot';

//temporarily set
$authenticated = TRUE;

setReporting();
removeMagicQuotes();
unregisterGlobals();

if ($authenticated) {
    //do stuff
    try {

        echo "\$url pre parse: $url";

        $parseUrl = new ParseUrl($url);
        $db = new Database($config);

        echo '<br />controller: ' . $parseUrl->getController();
        echo '<br />action: ' . $parseUrl->getAction();
        echo '<br />queryString: ' . $parseUrl->getQueryString();

        $model = $parseUrl->getModel();
        $controller = $parseUrl->getController();
        $action = $parseUrl->getAction();
        $view = $parseUrl->getView();
        $queryString = $parseUrl->getQueryString();
        $dispatch = new $controller($model,$config,$controller,$action,$view);
	
        //figure out what the heck this does and then try using it
        if ((int)method_exists($controller, $action)) {

            //this calls the method $action of the object $dispatch 
            //and passes it the argument $queryString
            //call_user_func_array(array($dispatch,$action),$queryString);

            //I think this does the same thing as call_user_func_array
            echo $dispatch->$action($queryString);

        } else {
            /* Error Generation Code Here */
        } 

    } catch (Exception $Exception) {

        echo 'Oops! ' . $Exception->getMessage();
    }

} else {

    echo "Please login:";
    echo "Username:<br />";
    echo "Password:<br />";
    echo "Don't have a login? Create an account.";

}

