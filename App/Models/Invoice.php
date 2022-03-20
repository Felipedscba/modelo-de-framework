<?php 

namespace App\Models;
use System\Model;

class Invoice extends Model
{
	protected $table    = 'invoices';
	protected $fillable = [];

	protected $useTimestamps = true;
	protected $useSoftDelete = false;

	protected $callbacks = [];
}
