<?php 

namespace App\Migrations;
use Closure;

class mig_1647782747_CreateUsers 
{
	public function up(Closure $call) 
	{
		$call("
			create table if not exists users (
				id int unsigned primary key not null auto_increment,

				name varchar(191) not null,
				email varchar(191) not null,
				password varchar(62) not null,
				account_type enum('cliente', 'administrador') not null default 'cliente',
				active boolean not null default true,

				created_at timestamp not null,
				updated_at timestamp not null
			)Engine=InnoDB
		");

		$this->populate();
	}

	public function down(Closure $call)
	{
		$call('drop table if exists users');
	}

	public function populate() 
	{
		
	}
}