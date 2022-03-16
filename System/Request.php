<?php

namespace System;

class Request {
	public function getPost($param) {
		return $_POST[$param] ?? null;
	}

	public function getGet($param) {
		return $_GET[$param] ?? null;
	}
}