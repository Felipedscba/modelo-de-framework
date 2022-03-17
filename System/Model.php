<?php

namespace System;

class Model
{
	protected $pk    = 'id';
	protected $table = null;

	protected $callbacks = [];

	private $_where   = [];
	private $_whereP  = [];
	private $_join    = [];
	private $_fields  = [];
	private $_orderBy = '';

	public function find($id = null)
	{
		if(!is_null($id)) {
			$this->where($this->pk, $id);
		}

		$r = dbFindFirst($this->getSelectSql(), $this->_whereP);

		$this->clear();

		return $r;
	}
	
	public function get($limit = null, $offset = null)
	{
		$sql = $this->getSelectSql($limit, $offset);
		$r   = dbFindAll($sql, $this->_whereP);

		$this->clear();

		return $r;
	}

	public function fields($fields):Model
	{
		if(is_array($fields)) {
			array_push($this->_fields, ...$fields);
		} else {
			$this->_fields[] = $fields;
		}
		return $this;
	}

	public function orderBy($orderBy, $dir = 'desc'):Model
	{
		$this->_orderBy = $orderBy.' '.$dir;
		return $this;
	}

	public function where($field, $value = null, $isSql = false):Model
	{
		$sql = $field;

		if(strpos(trim($field), ' ') !== false) {
			$isSql = true;
		}

		if(!$isSql) {
			if(is_array($value)) {
				$sql .= ' in ('.placeholders(count($value)).')';
			} else {
				$sql .= ' = ?';
			}
		}

		if(is_array($value)) {
			$this->_whereP = array_merge($this->_whereP, $value);
		} else {
			if(!is_null($value)) {
				$this->_whereP[] = $value;
			}
		}

		$this->_where[] = $sql;
		
		return $this;
	}

	public function getSelectSql($limit = null, $offset = null) 
	{
		$where  = $this->getWhere();

		$limit   = $limit ? ' limit '.$limit : '';
		$offset  = $limit && $offset ? ', '.$offset : '';
		$join    = count($this->_join) > 0 ? implode(' ', $this->_join) : '';
		$fields  = count($this->_fields) > 0 ? implode(', ', $this->_fields) : '*';
		$orderBy = $this->_orderBy ? ' order by '.$this->_orderBy.' ' : '';

		return 'select '.$fields.' from '.$this->table.' '.$join .' '.$where.$orderBy.$limit.$offset;
	}

	public function count()
	{
		$sql = $this->getSelectSql();
		$sql = "select count(*) as TOTAL from (\n$sql\n) rcst_total";

		$total = intval(dbFindFirst($sql, $this->_whereP)['TOTAL']);
		return $total;
	}

	public function isValidPk($data)
	{
		return (($data[$this->pk] ?? 0) ?: 0) > 0;
	}

	public function save($data)
	{
		$novo = !$this->isValidPk($data);

		if($novo) {
			$this->insert($data);
			return db()->lastInsertId();
		} else {
			$id = $data[$this->pk];

			unset($data[$this->pk]);
			$this->update($data, $id);

			return $id;
		}
	}

	public function insert($data)
	{
		$this->dispatchCallbacks('insertBefore', $data);

		$keys = implode(', ', array_keys($data));
		$sql = 'insert into '.$this->table.' ('.$keys.') values('.placeholders(count($data)).')';

		dbPrepareExecute($sql, array_values($data));

		$data[$this->pk] = db()->lastInsertId();

		$this->dispatchCallbacks('insertAfter', $data);

		return $data[$this->pk];
	}

	public function update($data, $id = null)
	{
		if($id) {
			$this->where($this->pk, $id);
		}

		if(count($data) == 0) {
			throw new \Exception("Não há dados para o update");
		}

		$where = $this->getWhere();

		if(!$where) {
			$this->clear();
			throw new \Exception("Nenhuma condição informada para o update");
		}

		$this->dispatchCallbacks('updateBefore', $data);

		$sets = array_map(function($val) {
			return $val.' = ?';
		}, array_keys($data));

		$sql    = 'update '.$this->table.' set '.implode(', ', $sets).' '.$where;
		$params = array_values(array_merge($data , $this->_whereP));

		$this->clear();

		$state = dbPrepareExecute($sql, $params);

		$this->dispatchCallbacks('updateAfter', $data);

		return $state;
	}

	private function getWhere() {
		return count($this->_where) > 0 ? ' where '.implode(' and ', $this->_where) : '';
	}

	public function delete($id = null) 
	{
		if(!is_null($id)) {
			$this->where($this->pk, $id);
		}

		$where = $this->getWhere();

		if(!$where) {
			$this->clear();
			throw new \Exception("Nenhuma condição informada para o delete");
		}

		$this->dispatchCallbacks('deleteBefore', $data);

		$sql    = 'delete from '.$this->table.$where;
		$params = $this->_whereP;

		$this->clear();

		$state = dbPrepareExecute($sql, $params);

		$this->dispatchCallbacks('deleteAfter', $data);

		return $state;
	}

	public function join($table, $condition, $side = 'left'): Model
	{
		$this->_join[] = "$side join $table on $condition";
		return $this;
	}

	public function all()
	{
		return dbFindAll('select * from '.$this->table);
	}

	public function clear()
	{
		$this->_where  = [];
		$this->_whereP = [];
		$this->_join   = [];
		$this->_fields = [];
	}

	private function dispatchCallbacks($name, &$payload)
	{
		if(isset($this->callbacks[$name])){

			$callbacks = is_array($this->callbacks[$name]) ? $this->callbacks[$name] : [$this->callbacks[$name]];

			foreach($callbacks as $funcName) {
				$callbacks->$funcName($payload, $name);
			}
		}
	}
}