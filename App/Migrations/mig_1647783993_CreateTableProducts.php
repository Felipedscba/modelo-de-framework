<?php 

namespace App\Migrations;
use Closure;

class mig_1647783993_CreateTableProducts 
{
	public function up(Closure $call) 
	{
		$call("
			create table if not exists products (
				id int unsigned primary key not null auto_increment,
				category_id int unsigned not null references product_categories(id),

				name varchar(191) not null,
				description text not null,
				images json not null,
				price numeric(12, 2) not null,
				active boolean not null default true,
				
				created_at timestamp not null,
				updated_at timestamp not null
			)Engine=InnoDB
		");
	}

	public function down(Closure $call)
	{
		$call('drop table if exists products');
	}
}