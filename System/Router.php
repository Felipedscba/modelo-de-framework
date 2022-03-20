<?php

namespace System;

class Router {

	private $routes = [
		'GET'    => [],
		'POST'   => [],
		'DELETE' => [],

		'CLI'	 => []
	];

	public $_is_cli = false;
 
	private $middlewares = [];
	private $currentPath = null;
	private $namespace   = '';
	private $prefix  	 = '';
	private $useCache 	 = false;

	private $nameForSaveCache = null;

	private $cliParamsForRequest = null;

	public function __construct($baseUrl = '', $cliParams = null) {

		if($cliParams) {

			$this->currentPath = $cliParams[0] ?? '';
			$this->method  	   = 'CLI';
			$this->_is_cli     = true;

			$this->cliParamsForRequest = $cliParams;

		} else {
			$this->currentPath = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
			$this->method  	   = $_SERVER['REQUEST_METHOD'];

			if($baseUrl) {
				$baseUrl = strtolower($baseUrl);

				$scheme = $_SERVER['REQUEST_SCHEME'];
				$host	= $_SERVER['HTTP_HOST'];
				$port   = $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'];

				$this->currentPath = str_replace($baseUrl, '', "$scheme://$host$port".$this->currentPath) ?: '/';
			}
		}

	}

	public function use_cache() {
		$this->useCache = true;
	}

	public function middleware($path, $callback, $params = [])
	{
		$this->middlewares[] = [
			'path'     => $path,
			'callback' => $callback,
			'params'   => []
		];
	}

	public function loadCache($name, $force = false):bool
	{
		if(!$this->useCache) return false;

		$filename = ROOTPATH."/cache/system-routes/$name.php";

		if(!file_exists($filename) || $force) {
			$this->nameForSaveCache = $name;
			return false;
		}

		$cache = require $filename;

		$this->middlewares = $cache['middlewares'];
		$this->routes 	   = $cache['routes'];

		return true;
	}

	public function namespace(string $namespace) {
		$this->namespace = $namespace ? '\\'.$namespace : '';
	}

	public function prefix(string $prefix) {
		$this->prefix = '\\'.$prefix;
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
		$this->routes[$method][$path] = gettype($callback) == 'string' && $this->namespace ? $this->namespace.'\\'.$callback : $callback;
	}

	public function run() {
		if($this->nameForSaveCache) {
			$this->saveCache();
		}

		$request =  new Request($this->_is_cli ? $this->cliParamsForRequest : null);

		$path   = $this->currentPath;
		$method = $this->method; 
		
		$routeAction = null;

		if(isset($this->routes[$method][$path])) {
			$routeAction = $this->routes[$method][$path];
		}

		if($routeAction) {
			$this->runMiddlewares($request);
			$this->callController($routeAction, $request);
		} else {
			if($this->_is_cli) {
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

				$params = $middleware['params'] ?? [];

				if(gettype($middleware['callback']) == 'string') {
					$className = config('middlewares')[$middleware['callback']] ?? null;
					if(!$className) {
						throw new Exception('Middleware ['.$middleware['callback'].'] não localizado.');
					}
					$obj = new $className;
					$obj($request, ...$params);
				} else {
					$middleware['callback']($request, ...$params);
				}
			}
		}
	}

	function callController($input, Request $request) {

		if(gettype($input) == 'string') {
			$parts = explode('@', $input);

			$className = $parts[0][0] == '\\' ? $parts[0] : '\\App\\Controllers\\'.$parts[0];

			$obj   = new $className();
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

	private function saveCache() {
		$name = $this->nameForSaveCache;
			
		$str = implode("\n", [
			'<?php',
			'return [',
			"'routes' =>",
			var_export($this->routes, true),
			',',
			"'middlewares' =>",
			var_export($this->middlewares, true),
			'];'
		]);

		file_put_contents(ROOTPATH."/cache/system-routes/$name.php", $str);
	}
}