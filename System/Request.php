<?php

namespace System;

class Request {

	private $cliFlags  = [];
	private $cliParams = [];
	private $isCliRequest = false;

	public function __construct($cliParams = null)
	{
		if($cliParams) {
			$this->isCliRequest = true;
			$this->loadCliParams($cliParams);
		}
	}

	public function getPost($param)
	{
		return request($param);
	}

	public function getGet($param)
	{
		return request($param);
	}

	public function getCli($index)
	{
		return $this->cliParams[$index] ?? null;
	}

	public function validate(array $rules, $fieldNames = [], $values = null)
	{
		$params = $values ?: ($this->isCliRequest ? $this->cliParams : request());

		$verifyErrors = new Validation($params, $fieldNames);

		$verifyErrors->validate($rules);

		if($verifyErrors->has()) {
			if($this->isCliRequest) {
				$errorsGroup = $verifyErrors->all();
				foreach($errorsGroup as $errors) {
					echo "Falha ao executar o comando.\n";
					echo implode("\n", $errors)."\n";
				}
				exit;
			} else {
				oldSave($params);
				flash('errors', $errors);
				redirect($_SERVER["HTTP_REFERRER"] ?? session('_fm')['lastRoute'] ?? baseUrl('/'), false);
				exit;
			}
		}

		return $verifyErrors->filtered();
	}

	public function loadCliParams($cliParams)
	{
		$params = array_slice($cliParams, 1);
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

}