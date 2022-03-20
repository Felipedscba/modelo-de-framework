<?php

namespace App\Controllers\CLI;
use System\Request;

class MakeFilesController {

	function controller(Request $req) {
		$req->validate([
			0 => 'required|min:2'
		], [0 => 'Nome do controller']);

		$req->getCli(0);

		if($req->getCli('model')) {
			self::makeModel($req->getCli(0));
		}
	}

	function migration(Request $req) {
		$params = $req->validate([
			0 => 'required|min:2'
		], [0 => 'Nome da migration']);

		$this->makeMigration($params[0]);
	}

	function model(Request $req) {
		$params = $req->validate([
			0 => 'required|min:2'
		], [0 => 'Nome da model']);

		$this->makeModel($params[0]);
	}

	public static function makeController($name, $flags = []) {
		
	}

	public static function makeMigration($name, $flags = [])
	{

		$namespace = 'App\\Migrations';
		$template  = file_get_contents(ROOTPATH.'App\\Controllers\\CLI\\_templates\\Migration.php');

		$className = 'mig_'.time()."_{$name}";

		$template = "<?php \n\n".str_replace(
			['{className}'], 
			[$className], 
			$template
		);

		$filename = ROOTPATH.$namespace.'\\'.$className.'.php';

		file_put_contents($filename, $template);
		echo "\nMigration '$className' criado com successo.\n";	

		exec("\"$filename\" & exit");
	}

	public static function makeModel($name, $flags = [])
	{
		$parts = explode('\\', $name);

		if(count($parts) > 1) {
			$name = array_pop($parts);
		} else {
			$parts = [];
		}

		$className = strtoupper($name[0]).substr($name, 1);

		$name = str_to_plural($name);

		$index = 0;

		$name = preg_replace_callback('/[A-Z]/', function($word) use(&$index) {
			$index++;
			return ($index > 1 ? '_' : '').strtolower($word[0]);
		}, $name);

		if(count($parts) > 0) {
			$parts = array_map(fn($w) => strtoupper($w[0]).substr($w, 1), $parts);
		}

		$namespace = 'App\\Models'.(count($parts) > 0 ? '\\'.implode('\\', $parts) : '');
		$content   = ROOTPATH.$namespace.$className.'.php';

		$modelTemplate = file_get_contents(ROOTPATH.'App\\Controllers\\CLI\\_templates\\Model.php');

		$modelTemplate = "<?php \n\n".str_replace(
			['{namespace}', '{className}', '{tableName}'], 
			[$namespace, $className, $name], 
			$modelTemplate
		);
		
		if(!is_dir(ROOTPATH.$namespace)) {
			mkdir(ROOTPATH.$namespace, 511, true);
		}

		$filename = ROOTPATH.$namespace.'\\'.$className.'.php';

		file_put_contents($filename, $modelTemplate);
		echo "\nModel '$className' criado com successo.\n";

		exec("\"$filename\" & exit");
	}

}