<?php

return [
	'baseUrl' => 'http://localhost:81/',

	'middlewares' => [
		'guest' => \App\Middlewares\RedirectIfAuthenticate::class,
		'auth'  => \App\Middlewares\RedirectIfNotAuthenticate::class
	],

	'database' => [
		'user' => 'root',
		'pass' => ''

		'host' => 'localhost',
		'port' => 3308,
		'name' => ''
	],
	
	'mail' => [

	]
];