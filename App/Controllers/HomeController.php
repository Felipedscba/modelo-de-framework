<?php

namespace App\Controllers;
use System\Request;

class HomeController {
	public function login(Request $req) {
		$email    = $req->getGet('email');
		$password = $req->getGet('password');

		echo "Email: $email";
		echo '<br>';
		echo "Senha: $password";
	}

	public function loginForm() {
		$_SESSION['logado'] = true;
		redirect('/admin');
	}

	public function logout() {
		$_SESSION['logado'] = false;
		redirect('/auth/login');
	}

	public function register() {
		echo 'register';
	}

	public function home(Request $req) {
		echo 'home';
	}

	public function admin(Request $req) {
		echo 'tela admin';
	}
}