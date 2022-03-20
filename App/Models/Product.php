<?php 

namespace App\Models;
use System\Model;

class Product extends Model
{
	protected $table    = 'products';

	protected $fillable = [
		'category_id',
		'name',
		'description',
		'price',
		'images',
		'active'
	];

	protected $useTimestamps = true;
	protected $useSoftDelete = true;

	protected $callbacks = [
		'insertBefore' => 'parseImagesToJson',
		'updateBefore' => 'parseImagesToJson'
	];

	public function parseImagesToJson(&$data) {
		if(isset($data['images']) && is_array($data['images'])) {
			$data['images'] = json_encode($data['images'], JSON_PRETTY_PRINT);
		}
	}
}
