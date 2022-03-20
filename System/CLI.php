<?php

namespace System;

class CLI {
	private static $router = null;

	public static function call($command) {
		$args = preg_split('/( +)/', $command);
		$args = array_map($args, fn($word) => trim($word));

		self::run($args);
	}

	public static function run($args) {
		self::$router = new Router(null, $args);

		$router = self::$router;
		require ROOTPATH.'/Routes/cli.php';
		$router->run();
	}
}