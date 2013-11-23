<?php

/**
function MyAutoload($className)
{
    $filename = $className . '.php';
    require_once($filename);
}

$paths = array(get_include_path(), '../app/controllers', '../app/models', '../app/views');

//set include paths
set_include_path(implode(PATH_SEPARATOR, $paths));
 
// Next, register it with PHP.
spl_autoload_register('MyAutoload');

//test autoload (this clas hasn't explicitly been required or included)
$var = new MyClass();
$var->test();
*/

function requiredFiles($className)
{
    $paths = array('../lib/', '../app/controllers/', '../app/models/', '../app/views/');
    $classFound = false;

    //find files and require them
    foreach ($paths as $path) {
        $filename = $path . $className . '.php';
        if (file_exists($filename)) {
            require_once($filename);
            $classFound = true;
            break;
//        //functional testing
//        //eventually this else block won't be needed
//        } else {
//             echo '<p>' . $filename . '(' . print_r($classFound) . ')</p>';
        }
    }

    //if the file was not found at any of the paths, throw a 404
    if ($classFound === false) {
        throw404();
    }
}

// Next, register it with PHP.
spl_autoload_register('requiredFiles');
