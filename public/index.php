<?php

define('DS', DIRECTORY_SEPARATOR); 
define('ROOT', dirname(dirname(__FILE__)));

require_once (ROOT . DS . 'app' . DS . 'config' . DS . 'env.php');
require_once (ROOT . DS . 'vendor' . DS . 'autoload.php');
require_once (ROOT . DS . 'vendor' . DS . 'crondex' . DS . src . DS . Bootstrap . DS . 'functions.php');
require_once (ROOT . DS . 'vendor' . DS . 'crondex' . DS . src . DS . Bootstrap . DS . 'Bootstrap.php');

$app = new Crondex\Bootstrap;
