<?php

// Database functions

function db() {
	return \System\Connection::con();
}

function dbSQL($filename) {
	$filename = ROOTPATH.'App\\Sqls\\'.$filename;
	if(!file_exists($filename)) {
		throw new \Exception ('Arquivo SQL não localizado!');
	}
	$sql = file_get_contents($sql);

	return dbPrepareExecute($sql);
}

function dbPrepareExecute($sql, $params = []) {
	$ps = db()->prepare($sql);
	return $ps->execute($params);
}

function dbFindFirst($sql, $params = [])
{
	$sth = db()->prepare($sql);
	$sth->execute($params);
	
	return $sth->fetch();
}

function dbFindAll($sql, $params = [])
{
	$sth = db()->prepare($sql);
	$sth->execute($params);
	return $sth->fetchAll();
}

function dbCount($sql, $params = [])
{
	return dbFindFirst('select count(*) as total from ('.$sql.')', $params)['total'];
}

function dbPaginate(\System\Model $model)
{
	$page   = $_GET['page'] ?? 1;
	$limit  = $_GET['limit'] ?? 15;
	$offset = ($page - 1) * $limit;

	$total = $model->count();

	$r = $model->get($limit, $offset);

	$rest = $total % $limit;

	return [
		'pagination' => [
			'page'   => $page,
			'pages' => (($total - $rest) / $limit) + ($rest > 0 ? 1 : 0),
			'limit'  => $limit,
			'count'  => $total,
			'more'   => $total > ($offset + $limit - 1)
		],

		'result' => $r
	];
}

function getSql($name) {
	$baseDir = BASEDIR.'App'.DIRECTORY_SEPARATOR.'Sqls'.DIRECTORY_SEPARATOR;
	return file_get_contents($baseDir.$filename.'.sql');
}