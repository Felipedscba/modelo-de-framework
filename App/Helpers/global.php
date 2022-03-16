<?php

function redirect($url) {
	header('Location: '. BASE_URL.$url);
	exit;
}

function config($propName, $default = null) {
	return CONFIG[$propName] ?? $default;
}