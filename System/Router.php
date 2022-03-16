<?php

namespace System;

class Router {

	private static $routes = [
		'GET'    => [],
		'POST'   => [],
		'DELETE' => []
	];

	private $middlewares = [];
	private $currentPath = null;

	public function __construct() {
		$this->currentPath = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	}

	public function middleware($path, $callback)
	{
		$this->middlewares[] = [
			'path'     => $path,
			'callback' => $callback
		];
	}

	public function get($path, $callback)
	{
		self::$routes['GET'][strtolower($path)] = $callback;
	}

	public function post($path, $callback)
	{
		self::$routes['POST'][strtolower($path)] = $callback;
	}

	public function delete($path, $callback)
	{
		self::$routes['DELETE'][strtolower($path)] = $callback;
	}

	public function run() {
		$request = new Request();

		$path   = $this->currentPath;
		$method = $_SERVER['REQUEST_METHOD']; 
		
		$routeAction = null;

		if(isset(self::$routes[$method][$path])) {
			$routeAction = self::$routes[$method][$path];
		}

		if($routeAction) {
			$this->runMiddlewares($request);
			$this->callController($routeAction, $request);
		} else {
			http_response_code(404);
		}
	}

	private function runMiddlewares(Request $request) {
		foreach($this->middlewares as $middleware) {
			$path = str_replace('/', '\/', $middleware['path']);
			
			if(preg_match('/^'.$path.'/i', $this->currentPath)) {
				if(gettype($middleware['callback']) == 'string') {
					$className = config('middlewares')[$middleware['callback']] ?? null;
					if(!$className) {
						throw new Exception('Middleware ['.$middleware['callback'].'] não localizado.');
					}
					$obj = new $className;
					$obj();
				} else {
					$middleware['callback']($request);
				}
			}
		}
	}

	function callController($input, Request $request) {

		if(gettype($input) == 'string') {
			$parts = explode('@', $input);
			$className = '\\App\\Controllers\\'.$parts[0]; 

			$obj = new $className();

			call_user_func_array([$obj, $parts[1]], [$request]);
		} else {
			$input($request);
		}
	}
}