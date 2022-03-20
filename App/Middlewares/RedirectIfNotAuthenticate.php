<?php

namespace App\Middlewares;
use System\Request;

class RedirectIfNotAuthenticate {

	 public function __invoke(Request $req, $isAdmin = false) { 
		if(!user()) {
			redirect($isAadmin ? 'auth/login' : 'auth/login-admin');
		}
	}

}