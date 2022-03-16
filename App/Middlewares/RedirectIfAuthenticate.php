<?php

namespace App\Middlewares;

class RedirectIfAuthenticate {
	 public function __invoke() { 
		if(isset($_SESSION['logado']) && $_SESSION['logado']) {
			redirect('admin');
		}
	}
}