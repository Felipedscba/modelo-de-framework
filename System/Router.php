<?php

namespace System;

class Router {

	private static $routes = [
		'GET'    => [],
		'POST'   => [],
		'DELETE' => [],

		'CLI'	 => []
	];

	public static $_is_cli = false;

	private $middlewares = [];
	private $currentPath = null;
	private $namespace   = '';

	public function __construct() {
		$this->currentPath = self::$_is_cli ? (CLI_PARAMS[0] ?? '') : strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$this->method  	   = self::$_is_cli ? 'CLI' : $_SERVER['REQUEST_METHOD'];
	}

	public static function is_cli() {
		self::$_is_cli = true;
	}

	public function middleware($path, $callback)
	{
		$this->middlewares[] = [
			'path'     => $path,
			'callback' => $callback
		];
	}

	public function namespace(string $namespace) {
		$this->namespace = '\\'.$namespace;
	}

	public function get($path, $callback)
	{
		$this->add('GET', $path, $callback);
	}

	public function post($path, $callback)
	{
		$this->add('POST', $path, $callback);
	}

	public function cli($path, $callback) {
		$this->add('CLI', $path, $callback);
	}

	public function delete($path, $callback)
	{
		$this->add('DELETE', $path, $callback);
	}

	public function add($method, $path, $callback) {
		self::$routes[$method][$path] = gettype($callback) == 'string' ? $this->namespace.'\\'.$callback : $callback;
	}

	public function run() {
		$request = new Request();

		$path   = $this->currentPath;
		$method = $this->method; 
		
		$routeAction = null;

		if(isset(self::$routes[$method][$path])) {
			$routeAction = self::$routes[$method][$path];
		}

		if($routeAction) {
			$this->runMiddlewares($request);
			$this->callController($routeAction, $request);
		} else {
			if(self::$_is_cli) {
				echo "\nA rota CLI [$path] não foi localizada\n";
			} else {
				http_response_code(404);
			}
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

			$className = $parts[0][0] == '\\' ? $parts[0] : '\\App\\Controllers\\'.$parts[0];

			$obj = new $className();

			$return = call_user_func_array([$obj, $parts[1]], [$request]);

			if($return) {
				if(isset($return['_is_app_response'])) {
					sendResponse($return);
				} else {
					if(getype($return) == 'string') {
						echo $return;
					} else {
						json_encode($return, JSON_PRETTY_PRINT);
					}
				}
			}
		} else {
			$input($request);
		}
	}
}