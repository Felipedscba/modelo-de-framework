<?php

namespace App\Models;
use System\Model;

class User extends Model
{
	protected $table    = 'users';

	protected $fillable = [
		'email',
		'name',
		'password',
		'account_type',
		'active'
	];

	protected $useTimestamps = true;
	protected $useSoftDelete = false;

	protected $callbacks = ['beforeInsert' => 'hashPassword', 'beforeUpdate' => 'hashPassword'];

	public function hashPassword(&$data) 
	{
		if(isset($data['password'])) {
			$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		}
	}
}
