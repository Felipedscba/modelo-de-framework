<?php 

namespace App\Migrations;
use App\Models\ProductCategory;
use App\Models\Product;
use Closure;

class mig_1647784000_CreateTableProductCategories 
{
	public function up(Closure $call) 
	{
		$call("
			create table if not exists product_categories (
				id int unsigned primary key not null auto_increment,

				name varchar(191) not null,
				image varchar(191) not null,
				description varchar(191) not null,
				active boolean not null default true,

				created_at timestamp not null,
				updated_at timestamp not null
			)Engine=InnoDB
		");

		$this->populate();
	}

	public function down(Closure $call)
	{
		$call('drop table if exists product_categories');
	}

	public function populate() {
		$modelCategory = new ProductCategory();
		$modelProdut   = new Product();
		
		$data = [
			'category' => [
				'name' 		  => 'Notebooks',
				'image' 	  => 'notebook.png',
				'description' => 'Os notebooks mais modernos do mercado',

				'products' => [
					[
						'name' 		  => 'Notebook Asus VivoBook X543MA-GQ1300T - Intel Celeron Dual-Core 4GB 500GB 15,6” Windows 10',
						'description' => 'O Notebook Asus VivoBook X543MA-GQ1300T foi projetado para quem procura produtividade e versatilidade em um só produto. Execute todas as tarefas do seu dia a dia com velocidade e eficiência através da configuração moderna que traz processador Intel Celeron Dual-Core, 4GB de memória RAM e armazenamento com HD de 500GB.',
						'price' 	  => 49.99,
						'images'	  => ['product_01.png']
					],
					[
						'name' 		  => 'Notebook Asus VivoBook X543MA-GQ1300T - Intel Celeron Dual-Core 4GB 500GB 15,6” Windows 10',
						'description' => 'O Notebook Asus VivoBook X543MA-GQ1300T foi projetado para quem procura produtividade e versatilidade em um só produto. Execute todas as tarefas do seu dia a dia com velocidade e eficiência através da configuração moderna que traz processador Intel Celeron Dual-Core, 4GB de memória RAM e armazenamento com HD de 500GB.',
						'price' 	  => 49.99,
						'images'	  => ['product_01.png', 'product_02.png']
					],
					[
						'name' 		  => 'Notebook Asus VivoBook X543MA-GQ1300T - Intel Celeron Dual-Core 4GB 500GB 15,6” Windows 10',
						'description' => 'O Notebook Asus VivoBook X543MA-GQ1300T foi projetado para quem procura produtividade e versatilidade em um só produto. Execute todas as tarefas do seu dia a dia com velocidade e eficiência através da configuração moderna que traz processador Intel Celeron Dual-Core, 4GB de memória RAM e armazenamento com HD de 500GB.',
						'price' 	  => 49.99,
						'images'	  => ['product_02.png', 'product_01.png']
					],
				]
			]
		];

		foreach($data as $category) {
			$id = $modelCategory->insert($category);

			foreach($category['products'] as $product) {
				$product['category_id'] = $id;
				$modelProdut->insert($product);
			}
		}

	}
}