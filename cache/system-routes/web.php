<?php
return [
'routes' =>
array (
  'GET' => 
  array (
    'auth/login' => 'HomeController@login',
    'auth/register' => 'HomeController@register',
    'auth/loginForm' => 'HomeController@loginForm',
    '/' => 'HomeController@index',
    'admin' => 'HomeController@admin',
    'admin/logout' => 'HomeController@logout',
  ),
  'POST' => 
  array (
  ),
  'DELETE' => 
  array (
  ),
  'CLI' => 
  array (
  ),
)
,
'middlewares' =>
array (
  0 => 
  array (
    'path' => 'auth',
    'callback' => 'guest',
  ),
  1 => 
  array (
    'path' => 'admin',
    'callback' => 'auth',
  ),
)
];