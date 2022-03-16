<?php

namespace App\Middlewares;

class RedirectIfNotAuthenticate {
	 public function __invoke() { 
		if(!isset($_SESSION['logado']) || !$_SESSION['logado']) {
			redirect('auth/login');
		}
	}
}