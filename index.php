<?php

use System\Request;
use System\Router;

require './vendor/autoload.php';

define('CONFIG', require './config.php');
define('BASE_URL', CONFIG['baseUrl']);

session();

$router = new Router();

require './routes.php';

$router->run();