<?php

function session($key = null, $value = null)
{
	if(!defined('SESSION_INIT')) {
		session_start();
		define('SESSION_INIT', true);
		if(!isset($_SESSION['_fm'])) {
			$_SESSION['_fm']['flash'] = [];
		}
		flashAge();
	}

	if(is_null($value)) {
		return $key ? ($_SESSION[$key] ?? null) : $_SESSION;
	} else {
		$_SESSION[$key] = $value;
	}
}

function oldSave($params = []) {
	flash('_fw_old', $params);
}

function old($key, $fallback = null) {
	return flash('_fw_old')[$key] ?? $fallback;
}

function flash($key, $value = null) {
	if(!is_null($value)) {
		$_SESSION['_fm']['flash'][$key] = [
			'value' => $value,
			'life'  => 1
		];
	}
	return $_SESSION['_fm']['flash'][$key]['value'] ?? null;
}

function flashAge() {
	$removeKeys = [];
	foreach($_SESSION['_fm']['flash'] as $key => &$flash) {
		if($flash['life'] == 0) {
			$removeKeys[] = $key;
		} else {
			$flash['file']--;
		}
		unset($flash);
	}

	foreach($removeKeys as $key) {
		unset($_SESSION['_fm']['flash'][$key]);
	}
}