<?php

use System\Request;
use System\Router;

require './vendor/autoload.php';

define('CONFIG', require './config.php');
define('BASE_URL', CONFIG['baseUrl']);

session_start();

$router = new Router();

$router->middleware('/auth', 'guest');
$router->middleware('/admin', 'auth');

$router->get('/auth/login', 'HomeController@login');
$router->get('/auth/register', 'HomeController@register');
$router->get('/auth/loginForm', 'HomeController@loginForm');

$router->get('/admin', 'HomeController@admin');
$router->get('/admin/logout', 'HomeController@logout');

$router->run();