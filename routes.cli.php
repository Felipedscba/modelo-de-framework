<?php

use System\Request;

$router->cli('make:controller', function(Request $req) {
	$req->validate([
		'model' => 'required'
	]);
});