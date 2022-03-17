<?php

use System\Request;

$router->cli('make:controller', function(Request $req) {
	echo "\nEsta é uma rota CLI de teste\n";
});