<?php

use System\Router;

require './vendor/autoload.php';

define('ROOTPATH', __DIR__.DIRECTORY_SEPARATOR);

session();

define('CONFIG', require ROOTPATH.'config.php');
define('BASE_URL', CONFIG['baseUrl']);

$router = new Router(BASE_URL);

$router->use_cache();

if(!$router->loadCache('web')) {
	require ROOTPATH.'Routes/web.php';
}

$router->run();
