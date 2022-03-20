<?php 

namespace App\Migrations;
use Closure;

class mig_1647804579_CreateTableInvoices 
{
	public function up(Closure $call) 
	{
		$call("
			create table if not exists invoices(
				id int unsigned primary key not null auto_increment,
				user_id int unsigned not null references users(id),

				preference_id int unsigned not null,

				value_discount numeric(10, 3) not null default 0,
				value_shipping numeric(10, 3) not null default 0,

				payment_method varchar(191) not null,
				payment_qtd smallint not null,
				payment_status varchar(50) not null,

				created_at timestamp not null,
				updated_at timestamp not null
			)Engine=InnoDB
			");
	}

	public function down(Closure $call)
	{
		$call('drop table invoices');
	}
}