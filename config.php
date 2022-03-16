<?php

return [
	'middlewares' => [
		'guest' => \App\Middlewares\RedirectIfAuthenticate::class,
		'auth'  => \App\Middlewares\RedirectIfNotAuthenticate::class
	],
	'baseUrl' => 'http://localhost:81/'
];