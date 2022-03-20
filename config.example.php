<?php

return [
	'baseUrl' => 'http://projetos-desenvolvimento.com/model-base-framework/',

	'middlewares' => [
		'guest' => \App\Middlewares\RedirectIfAuthenticate::class,
		'auth'  => \App\Middlewares\RedirectIfNotAuthenticate::class
	],

	'database' => [
		'user' => 'root',
		'pass' => '',

		'host' => 'localhost',
		'port' => 3308,
		'name' => ''
	],
	
	'mail' => [
		'type'     => 'smtp',
		'host'     => 'smtp.gmail.com',
		'port'     => '496',
		'user'     => '',
		'password' => ''
	]
];