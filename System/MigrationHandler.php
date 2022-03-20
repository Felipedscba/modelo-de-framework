<?php

namespace System;

class MigrationHandler {

	public static function run() {
		self::createTable();

		$last  = self::getLast();
		$files = self::getFiles($last);

		$batch = ($last['batch'] ?? 0) + 1;

		$executedObjs = [];

		foreach($files as $file) {
			$obj = null;
			try {
				$className = 'App\\Migrations\\'.substr($file, 0, strlen($file) - 4);
				$obj = new $className;
				$executedObjs[$file] = $obj;

				$obj->up(fn($sql) => dbPrepareExecute($sql));

				self::insertMigraton($file, $batch);

				echo "Success [$file]\n";
			} catch(\Exception $e) {
				echo "Error [$file] - ".$e->getMessage()."\n\n";
				$executedObjs = array_reverse($executedObjs);
				
				foreach($executedObjs as $file => $obj) {
					$obj->down(fn($sql) => dbPrepareExecute($sql));
					self::deleteMigraton($file);
					echo "Rollback [$file]\n";
				}
				break;
			}
		}
	}

	private static function insertMigraton($file, $batch) 
	{
		dbPrepareExecute('insert migrations(filename, batch) values(?, ?)', [ $file, $batch ]);
	}

	private static function deleteMigraton($file)
	{
		dbPrepareExecute('delete from migrations where filename = ?', [ $file ]);
	}

	private static function createTable()
	{
		dbPrepareExecute("create table if not exists migrations (
			id int not null primary key auto_increment,
			filename varchar(191) not null,
			batch int not null default 0
		)");
	}

	private static function getLast()
	{
		return dbFindFirst('select * from migrations order by id desc');
	}

	private static function getFiles($last = null) {
		$files = array_diff(scandir(ROOTPATH.'App\\Migrations'), ['.', '..']);

		if($last) {
			usort($files, fn($a, $b) => intval(substr($a, 4, 10)) - intval(substr($b, 4, 10)));

			$lastTime = intval(substr($last['filename'], 4, 10));

			$files = array_values(array_filter($files, function($filename) use($lastTime){
				return intval(substr($filename, 4, 10)) > $lastTime;
			}));
		}

		return $files;
	}
}