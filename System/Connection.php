<?php

namespace System;

class Connection 
{
	private static $con = null;

	public static function con()
	{
		if(!self::$con) {
			
			$host   = CONFIG['database']['host'] ?? 'localhost';
			$dbname = CONFIG['database']['name'];

			$user = CONFIG['database']['user'] ?? null;
			$pass = CONFIG['database']['pass'] ?? null;
			$port = CONFIG['database']['port'] ?? 3306;

			$dsn  = 'mysql:host='.$host.';dbname='.$dbname.';port='.$port.';charset=utf8mb4';
			
			if(!($host && $dbname && $user)) {
				throw new \Exception('Um dos parametros de conexão do DB está em branco [host, name, user]');
			}

			self::$con = new \PDO($dsn, 
				$user, 
				$pass, 
				array(
				    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				    \PDO::ATTR_DEFAULT_FETCH_MODE  => \PDO::FETCH_ASSOC
				)
			);
		}

		return self::$con;
	}
}