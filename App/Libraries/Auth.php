<?php

namespace App\Libraries;

class Auth {
	public function redirectIfNotAuthenticate() { 
		if(!isset($_SESSION['logado']) || !$_SESSION['logado']) {
			header('Location: /auth/login');
		}
	}
	public function redirectIfAuthenticate() {
		if(isset($_SESSION['logado']) && $_SESSION['logado']) {
			header('Location: /admin');
		}
	}
}
