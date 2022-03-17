<?php

namespace System;

class Request {

	private $cliFlags  = [];
	private $cliParams = [];

	public function __construct() {
		if(defined('CLI_PARAMS')) {
			$this->loadCliParams();
		}
	}

	public function getPost($param) {
		return request($param);
	}

	public function getGet($param) {
		return request($param);
	}

	public function validate(array $rules, $fieldNames = []) {
		$params = defined('CLI_PARAMS') ? $this->cliParams : request();

		$verifyErrors = new Validation($params, $fieldNames);

		$verifyErrors->validate($rules);

		if($verifyErrors->has()) {
			if(defined('CLI_PARAMS')) {
				$errorsGroup = $verifyErrors->all();
				foreach($errorsGroup as $errors) {
					echo "Falha ao executar o comando.\n";
					echo implode("\n", $errors)."\n";
				}
			} else {
				oldSave($params);
				flash('errors', $errors);
				redirect($_SERVER["HTTP_REFERRER"] ?? session('_fm')['lastRoute'] ?? baseUrl('/'), false);
				exit;
			}
		}

		return $verifyErrors->filtered();
	}

	public function loadCliParams() {
		$params = array_slice(CLI_PARAMS, 1);
		foreach($params as $arg) {
			if(($arg[0] ?? '') == '-'){
				$this->cliFlags[] = $arg;
			} else {
				$parts = explode('=', $arg);
				if(count($parts) > 1) {
					$this->cliParams[$parts[0]] = $parts[1];
				} else {
					$this->cliParams[] = $arg;
				}
			}
		}
	}

	public function hasFlag($flag)
	{
		return in_array('-'.$flag, $this->cliFlags);
	}

	public function getCli($index)
	{
		return $this->cliParams[$index] ?? null;
	}

}