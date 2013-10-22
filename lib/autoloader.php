<?php

function MyAutoload($className)
{
    require_once($className . '.php');
}

$paths = array(get_include_path(), '../app/controllers', '../app/models', '../app/views');

//set include paths
set_include_path(implode(PATH_SEPARATOR, $paths));
//print_r($paths); //debugging
 
// Next, register it with PHP.
spl_autoload_register('MyAutoload');

//test autoload (this clas hasn't explicitly been required or included)
$var = new MyClass();
$var->test();

