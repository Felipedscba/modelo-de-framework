<?php

$router->middleware('/auth', 'guest');
$router->middleware('/admin', 'auth');

$router->get('/auth/login', 'HomeController@login');
$router->get('/auth/register', 'HomeController@register');
$router->get('/auth/loginForm', 'HomeController@loginForm');

$router->get('/', function(\System\Request $req) {
	
});

$router->get('/admin', 'HomeController@admin');
$router->get('/admin/logout', 'HomeController@logout');
