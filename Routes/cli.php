<?php

use System\Request;

$router->namespace('App\\Controllers\\CLI');

$router->cli('db:create-user', 'DBController@createUser');
$router->cli('make:controller', 'MakeFilesController@controller');
$router->cli('make:model', 'MakeFilesController@model');
$router->cli('make:migration', 'MakeFilesController@migration');

$router->cli('migrate', 'MigrationController@migrate');

