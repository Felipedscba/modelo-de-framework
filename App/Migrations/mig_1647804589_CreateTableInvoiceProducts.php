<?php 

namespace App\Migrations;
use Closure;

class mig_1647804589_CreateTableInvoiceProducts 
{
	public function up(Closure $call) 
	{
		$call("
			create table if not exists invoices(
				id int unsigned primary key not null auto_increment,
				invoice_id int unsigned not null references invoices(id),
				product_id int unsigned not null references products(id),
				
				qtd smallint unsigned not null,
				price numeric(12, 2) not null,

				status varchar(25) not null,
				
				created_at timestamp not null,
				updated_at timestamp not null
			)Engine=InnoDB
		");
	}

	public function down(Closure $call)
	{

	}
}