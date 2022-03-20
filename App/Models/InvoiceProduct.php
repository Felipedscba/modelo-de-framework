<?php 

namespace App\Models;
use System\Model;

class InvoiceProduct extends Model
{
	protected $table    = 'invoice_products';
	protected $fillable = [];

	protected $useTimestamps = true;
	protected $useSoftDelete = false;

	protected $callbacks = [];
}
