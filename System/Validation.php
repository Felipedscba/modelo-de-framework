<?php

namespace System;

class Validation {

	private $errors   = [];
	private $filtered = [];

	private $data = [];
	private $fieldNames = [];

	private $messages = [
		'required' => 'O campo {field} deve ser informado'
	];

	public function __construct(array $data, array $fieldNames = []) {
		$this->fieldNames = $fieldNames;
		$this->data 	  = $data;
	}

	public function has(string $name = null) {
		if(is_null($name)) return count($this->errors) > 0;
		return count($this->errors[$name] ?? []) > 0;
	}

	public function first(string $name) {
		return $this->errors[$name][0];
	}

	public function append(string $name, $error) {
		if(isset($this->errors[$name])) {
			$this->errors[$name][] = $error;
		} else {
			$this->errors[$name] = [$error];
		}
	}

	public function get(string $name) {
		return $this->errors[$name];
	}

	public function all() {
		return $this->errors;
	}

	public function validate(array $rules, array $params = [])
	{
		foreach($rules as $field => $rules) {
			$rules   = is_array($rules) ? $rules : explode('|', $rules);
			$isValid = true;

			foreach($rules as $rule) {	
				if($rule == 'required') {
					if(!isset($this->data[$field]) || trim($this->data[$field]) == '' || $this->data[$field] === null) {
						$this->append($field, $this->getMessage($field, $rule));
						$isValid = false;
						break;
					}
				} else if(isset($this->data[$field])) {
					$parts = explode(':', $rule);
				}
			}

			if($isValid) {
				$this->filtered[$field] = $this->data[$field];
			}
		}

		return $this->has();
	}

	public function getMessage($field, $rule)
	{
		$field = $this->fieldNames[$field] ?? $field;
		$value = $this->data[$field] ?? null;

		if(is_array($value) || is_object($value)) {
			$value = json_encode($value);
		}

		return str_replace(['{field}', '{value}'], [$field, $value], ($this->messages[$rule] ?? $rule));
	}

	public function filtered() {
		return $this->filtered;
	}
}