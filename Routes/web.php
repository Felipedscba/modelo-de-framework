<?php

// Middlewares

$router->middleware('auth', 'guest');
$router->middleware('admin', 'auth');

// Rotas

$router->get('auth/login', 'HomeController@login');
$router->get('auth/register', 'HomeController@register');
$router->get('auth/loginForm', 'HomeController@loginForm');

$router->get('/', 'HomeController@index');

$router->get('admin', 'HomeController@admin');
$router->get('admin/logout', 'HomeController@logout');
