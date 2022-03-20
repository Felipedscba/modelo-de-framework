<?php 

namespace App\Models;
use System\Model;

class ProductCategory extends Model
{
	protected $table    = 'product_categories';
	protected $fillable = [
		'name',
		'image',
		'description',
		'active'
	];

	protected $useTimestamps = true;
	protected $useSoftDelete = false;

	protected $callbacks = [];
}
