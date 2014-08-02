<?php

define('DS', DIRECTORY_SEPARATOR); 
define('ROOT', dirname(dirname(__FILE__)) . DS);

require_once (ROOT . 'app' . DS . 'config' . DS . 'env.php');
require_once (ROOT . 'vendor' . DS . 'autoload.php');
require_once (ROOT . 'vendor' . DS . 'crondex' . DS . src . DS . Bootstrap . DS . 'Bootstrap.php');

$configFilePath = ROOT . 'app' . DS . 'config' . DS . 'main.ini';
$routesFilePath = ROOT . 'app' . DS . 'config' . DS . 'routes.ini';

$app = new Crondex\Bootstrap($configFilePath, $routesFilePath);
