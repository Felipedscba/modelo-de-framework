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

function oldSave($params = null) {
	flash('_fw_old', $params ? $params : session());
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
