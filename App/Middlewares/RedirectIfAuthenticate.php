<?php

namespace App\Middlewares;
use System\Request;

class RedirectIfAuthenticate {
	 public function __invoke(Request $req, $isAdmin = false) {
		if(user()) {
			redirect($isAadmin ? 'admin' : 'area-do-cliente');
		}
	}
}