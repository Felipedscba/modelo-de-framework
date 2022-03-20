<?php

namespace App\Controllers;
use System\Request;

class AuthController {

	public function login()
	{
		view('auth/login.php');
	}

	public function loginAdmin() {
		view('auth/login-admin.php');
	}

	public function loginForm(Request $req)
	{
		self::handleLoginRequest($req, $isAdmin);
	}

	public static function handleLoginRequest(Request $req, $isAdmin = false)
	{
		$params = $req->validate([
			'email'    => '',
			'password' => ''
		]);

		$model = new UserModel();
		$user  = $model->where('email', $email)->find();

		$verified = password_verify($params['password'], $user['password']);

		if(!$verified) {
			oldSave();
			redirectBack();
		}

		session('user_id', $user['id']);

		if($isAadmin) {
			redirect('admin');
		} else {
			redirect('/');
		}
	}
}
