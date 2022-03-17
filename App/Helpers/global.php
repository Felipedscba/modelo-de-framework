<?php

function baseUrl(string $url)
{
	return CONFIG['baseUrl'].($url[0] == '/' ? substr($url, 1) : $url);
}

function config(string $key, $default = null) {
	return CONFIG[$key] ?? $default;
}

// Others functions

function request($key = null)
{
	if(!defined('REQUEST_DATA')) {
		if(strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') {
			define('REQUEST_DATA', $_GET);
		} else {
			$content = file_get_contents('php://input');
			define('REQUEST_DATA', count($_POST) > 0 ? $_POST : json_decode($content, true));
		}
	}
	return $key ? (REQUEST_DATA[$key] ?? null) : REQUEST_DATA;
}

function user($key = null, $id = null, $forceReload = false)
{	 
	$user_row = null;

	if(is_null($id) && is_null($GLOBALS['user'] ?? null)){
		$id = session('user_id');
		$forceReload = true;
	} else if(is_null($id)){
		$user_row = $GLOBALS['user'] ?? null;
	}

	if($id) {
		$user_row = model('Users')->find($id);
	}

	if($forceReload) {
		$GLOBALS['user'] = $user_row;
	}

	return $user_row ? ($key ? ($user_row[$key] ?? null) : $user_row) : null;
}

function redirect($path, $useBaseUrl = true) {
	header('Location: '.($useBaseUrl ? baseUrl($path) : $path));
	exit;
}

function responseJson($data, $code = 200) {
	return [
		'_is_app_response' => true,
		'type'    => 'json',
		'code'    => $code,
		'payload' => $data
	];
}

function view($name, $params = []) {
	$name     = str_replace('/', '\\', $name);
	$filename = BASEDIR.'Resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$name;
	$filename = strpos($filename, '.') !== false ? $filename : $filename.'.php';

	if(!file_exists($filename)) {
		throw new \Exception('Arquivo view "'.$name.'" não localizado');
	}

	ob_start();
	foreach($params as $key => $value) {
		$$key = $value;
	}

	require $filename;
	$contents = ob_get_clean();
	
	return $contents;
}

function responseHtml(string $file, $params = [], $code = 200)
{
	return [
		'_is_app_response' => true,
		'type'    => 'html',
		'code'    => $code,
		'payload' => view($file, $params)
	];
}

function sendResponse(array $response) {
	if($response['type'] == 'json') {
		header('Content-type: application/json;charset=utf-8');
		echo json_encode($response['payload'], JSON_PRETTY_PRINT);
	} else if($response['type'] == 'html') {
		header('Content-type: text/html;charset=utf-8');
		echo $response['payload'];
	}
	http_response_code($response['code']);
	exit;
}

function model($name)
{
	if(!isset($GLOBALS['models'])) {
		$GLOBALS['models'] = [];
	}

	$model = null;

	if(!($model = $GLOBALS['models'][$name] ?? null)) {
		$className = '\\App\\Models\\'.$name.'Model';
		$model = new $className;
		$GLOBALS['models'][$name] = $model;
	}

	return $model;
}

function validate(array $fieldsRules, $params = null) {
	if(is_null($params)) {
		$params = request();
	}

	$invalids = [];

	foreach($fieldsRules as $field => $rulesGroup) {
		$rules  = explode('|', $rulesGroup);
		$errors = [];

		foreach($rules as $rule) {
			switch ($rule) {
				case 'required':
					if(!($params[$field] ?? null)) {
						$errors[] = 'Prencha o campo '.$field;
					}
					break;
			}
		}

		if(count($errors) > 0) {
			$invalids[$field] = $errors;
		}
	}

	return count($invalids) > 0 ? $invalids : true;
}

function placeholders($qtd) {
	$str = str_repeat('?, ', $qtd);
	$str = substr($str, 0, strlen($str) - 2);
	
	return $str;
}