<?php

namespace App\Controllers\CLI;
use System\Request;
use System\MigrationHandler;

class MigrationController
{
	public function migrate(Request $req) 
	{
		MigrationHandler::run();
	}	
}