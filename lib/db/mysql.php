<?php
/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

class DB {
	private static $link = null;
	private static $info = array(
		'last_query' => null,
		'num_rows' => null,
		'insert_id' => null
	);

	private static $where = '';
	private static $group = '';
	private static $order = '';
	private static $limit = '';

	private static $option = [];

	public static function lastQuery() {
		return self::$info['last_query'];
	}

	public static function insertId() {
		return self::$info['insert_id'];
	}

	public static function numRows() {
		return self::$info['num_rows'];
	}

	private static function connect() {
		$o = self::$option;

		if(($l = mysql_connect(
			(isset($o['host'])   ? $o['host']   : 'localhost').':'.
			(isset($o['port'])   ? $o['port']   : 3306),
			 isset($o['user'])   ? $o['user']   : 'root',
			 isset($o['pass'])   ? $o['pass']   : ''
		)) && mysql_select_db(isset($o['name']) ? $o['name'] : 'default', $l)) {
			self::$link = $l;
			mysql_set_charset(isset($o['charset']) ? $o['charset'] : "utf8");
			mysql_query("SET time_zone = '".(
				isset($o['time_zone']) ? $o['time_zone'] : "Asia/Seoul"
			)."'");
			mysql_query("SET AUTOCOMMIT=1");
		} else {
			die('Could not connect to MySQL database.');
		}
	}

	public static function init( /*array*/ $o) {
		self::$option = $o;
	}

	private static function __where($info, $type = 'AND') {
		$r = [];
		foreach($info as $field => $value){
			if(empty($field)) continue;
			if(is_array($value) && ($field == '(_AND_)' || $field == '(_OR_)')) {
				$type = $field == '(_OR_)' ? 'OR' : 'AND';
				$tmp = self::__where($value, $type);
				if(count($tmp) > 0) $r[] = '('.implode(' '.$type.' ', $tmp).')';
			} else {
				$operator = '=';
				//(=|<>|<=|>=|<|>|IN|LIKE|IS)
				if(preg_match("/(.+){(=|<>|<=|>=|<|>|[A-Z]+)}$/", $field, $m)) {
					$field = $m[1];
					$operator = $m[2];
					if($operator == 'IS') {
						if(strpos($field, '^') !== 0) $field = '^'.$field;
					} elseif($operator == 'IN') {
						if(strpos($field, '^') !== 0) $field = '^'.$field;
						$value = explode(',', $value);
						if(count($value)===0 || empty($value[0])) continue;
						foreach ($value as $k=>$v) $value[$k] = self::__quotes($v);
						$value = '('.implode(',', $value).')';
					}
				}
				if(strpos($field, '^') === 0)
					$field = substr($field, 1);
				else $value = self::__quotes($value);
				if(empty($field) && !empty($value)) $r[] = $value;
				else $r[] = sprintf("`%s`%s %s", $field, $operator, $value);
			}
		}
		return $r;
	}

	private static function __order($by, $order_type = 'DESC') {
		$group = [];
		$order = self::$order;
		if(!is_array($by)) $by = explode(',', $by);
		foreach($by as $field => $type){
			if(is_int($field) && !preg_match('/(DESC|desc|ASC|asc)/', $type)){
				$field = $type;
				$type = $order_type;
			}
			if(strtoupper($type) === 'GROUP') {
				$group[] = $field;
			} else {
				if($field === '^')
					$order .= sprintf("%s %s", empty($order)?'ORDER BY':',', $type);
				else
					$order .= sprintf("%s `%s` %s", empty($order)?'ORDER BY':',', $field, $type);
			}
		}
		self::$order = $order;
		self::$group = count($group) > 0 ? 'GROUP BY '.implode(',', $group) : '';
	}

	private static function __limit($limit) {
		self::$limit = 'LIMIT '.$limit;
	}

	private static function __extra() {
		$extra = '';
		if(!empty(self::$where)) $extra .= ' '.self::$where;
		if(!empty(self::$group)) $extra .= ' '.self::$group;
		if(!empty(self::$order)) $extra .= ' '.self::$order;
		if(!empty(self::$limit)) $extra .= ' '.self::$limit;
		// cleanup
		self::$where = '';
		self::$group = '';
		self::$order = '';
		self::$limit = '';
		return $extra;
	}

	private static function __quotes($val) {
		if($val === true || $val === false) { return (int)$val; }
		else if(is_int($val) || is_float($val)) { return $val; }
		else { return "'".self::escape($val)."'"; }
		//연결이 안되있을때도 동작하기 위해 str_replace 사용
		//return "'".mysql_real_escape_string($val)."'";
	}

	public static function query($qry, $params = [], $return = false) {
		if(self::$link === null) {self::connect();}
		if($params === true || is_callable($params)) {
			$return = $params !== true ? $params : true;
			$params = [];
		}
		if(!empty($params)) {
			if(!is_array($params)) $params = array_slice(func_get_args(), 1);
			$qry = preg_replace_callback('/:(\d+)/',
						function ($m) use ($params) {
						  return self::__quotes($params[$m[1] - 1]);
						}, $qry
					);
		}
		self::$info['last_query'] = $qry;
		$result = mysql_query($qry);
		if(mysql_errno()) {
			throw new Exception(mysql_error(), mysql_errno());
		} else {
			if(is_resource($result)){
				self::$info['num_rows'] = mysql_num_rows($result);
			}
			if(is_callable($return)){
				return $return($result);
			}elseif($return){
				$data = array();
				while($row = mysql_fetch_assoc($result)){
					$data[] = $row;
				}
				mysql_free_result($result);
				return $data;
			}
			return true;
		}
	}

	public static function __gets($table, $select = '*', $one = false, $callback = null) {
		if(self::$link === null) {self::connect();}
		$data = [];
		$sql = sprintf("SELECT %s FROM %s%s", $select, $table, self::__extra());
		self::$info['last_query'] = $sql;
		if(!($result = mysql_query($sql))){
			throw new Exception(mysql_error(), mysql_errno());
		}elseif(is_resource($result)){
			$num_rows = mysql_num_rows($result);
			self::$info['num_rows'] = $num_rows;
			if($num_rows !== 0){
				if($one){
					$data = mysql_fetch_assoc($result);
				}else{
					if(is_callable($callback)){
						return $callback($result);
					}
					while($row = mysql_fetch_assoc($result)){
						$data[] = $row;
					}
				}
			}
		}
		mysql_free_result($result);
		return $data;
	}

	private static function __sets($opts) {
		if(!empty($opts[0])) {
			$r = self::__where($opts[0]);
			if(count($r) > 0) self::$where = 'WHERE '.implode(' AND ', $r);
		}elseif(is_array($opts[0])) self::$where = 'WHERE 1';
		if(!empty($opts[1])) self::__order($opts[1]);
		if(!empty($opts[2])) self::__limit($opts[2]);
	}

	/*
	DB::get(_TABLE_)
	DB::get(_TABLE_, 'mb_srl', ['mb_id'=>'admin'])
	DB::get(_TABLE_, ['mb_id'=>'admin','mb_srl{>}'=>1], 'mb_id', '1,5')
	*/
	public static function get($table, $select = '*') {
		$anum = func_num_args();
		$args = func_get_args();
		$select = $anum > 1 ? $args[1] : '*';
		$i = 2;
		if(is_array($select)){
			$select = '*';
			$i--;
		}
		if($anum > $i) self::__sets(array_slice($args, $i));
		try{
			return self::__gets($table, $select, true);
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	public static function gets($table) {
		$callback = null;
		$anum = func_num_args();
		$args = func_get_args();
		$select = $anum > 1 ? $args[1] : '*';
		$i = 2;
		if(is_array($select)){
			$select = '*';
			$i--;
		}
		if($anum > $i) {
			if(is_callable($args[$anum - 1])){
				$callback = $args[$anum - 1];
				$args[$anum - 1] = null;
			}
			self::__sets(array_slice($args, $i));
		}
		try{
			return self::__gets($table, $select, false, $callback);
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/*
	DB::insert(_TABLE_, [_DATA_])
	*/
	public static function insert($table, $data) {
		if(self::$link === null) {self::connect();}
		$fields = '';
		$values = '';
		foreach($data as $col => $value){
			if(strpos($col, '^') === 0){
				$col = substr($col, 1);
			}
			else $value = self::__quotes($value);
			$fields .= sprintf("`%s`,", $col);
			$values .= sprintf("%s,", $value);
		}
		$fields = substr($fields, 0, -1);
		$values = substr($values, 0, -1);
		$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fields, $values);
		self::$info['last_query'] = $sql;
		if(!mysql_query($sql)){
			throw new Exception(mysql_error(), mysql_errno());
		}else{
			self::$info['insert_id'] = mysql_insert_id();
			return true;
		}
	}

	/*
	DB::update(_TABLE_, [_DATA_], ['mb_id'=>'admin','mb_srl{>}'=>1])
	*/
	public static function update($table, $data) {
		if(func_num_args() > 2) self::__sets(array_slice(func_get_args(), 2));
		if(empty(self::$where)){
			throw new Exception("Where is not set. Can't update whole table.", 1);
		}else{
			if(self::$link === null) {self::connect();}
			$update = '';
			foreach($data as $col => $value){
				if(strpos($col, '^') === 0)
					$col = substr($col, 1);
				else $value = self::__quotes($value);
				$update .= sprintf("`%s`=%s, ", $col, $value);
			}
			$update = substr($update, 0, -2);
			$sql = sprintf("UPDATE %s SET %s%s", $table, $update, self::__extra());
			self::$info['last_query'] = $sql;
			if(!mysql_query($sql)){
				throw new Exception(mysql_error(), mysql_errno());
			}else{
				return true;
			}
		}
	}

	/*
	DB::delete(_TABLE_, ['mb_id'=>'admin','mb_srl{>}'=>1])
	*/
	public static function delete($table) {
		if(func_num_args() > 1) self::__sets(array_slice(func_get_args(), 1));
		if(self::$link === null) {self::connect();}
		$sql = sprintf("DELETE FROM %s%s", $table, self::__extra());
		self::$info['last_query'] = $sql;
		if(!mysql_query($sql)){
			throw new Exception(mysql_error(), mysql_errno());
		}else{
			return true;
		}
	}

	/*
	DB::count(_TABLE_, ['mb_id'=>'admin','mb_srl{>}'=>1])
	*/
	public static function count($table) {
		if(func_num_args() > 1) self::__sets(array_slice(func_get_args(), 1));
		try{
			$result = self::get($table, 'COUNT(*) as cnt');
			return (int)$result['cnt'];
		} catch (Exception $e) {
			return -1;
		}
	}

	public static function foundRows() {
		try{
			$result = self::query("SELECT FOUND_ROWS() as c", true);
			return (int)$result[0]['c'];
		} catch (Exception $e) {
			return -1;
		}
	}

	public static function fetch($res, $type = 'assoc') {
		switch ($type) {
			case 'array':
				return mysql_fetch_array($res);
			break;
			case 'assoc':
				return mysql_fetch_assoc($res);
			break;
			case 'field':
				return mysql_fetch_field($res);
			break;
			case 'lengths':
				return mysql_fetch_lengths($res);
			break;
			case 'object':
				return mysql_fetch_object($res);
			break;
			case 'row':
				return mysql_fetch_row($res);
			break;
		}
		throw new Exception("Where is not type.", 1);
	}

	public static function transaction() {
		if(self::$link === null) {self::connect();}
		mysql_query("SET AUTOCOMMIT=0; START TRANSACTION");
	}

	public static function commit() {
		mysql_query("COMMIT; SET AUTOCOMMIT=1");
	}

	public static function rollback() {
		mysql_query("ROLLBACK; SET AUTOCOMMIT=1");
	}

	public static function exists($table, $column = '') {
		try {
			$params = [self::$option['name'], $table];
			$query = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :1 AND TABLE_NAME =:2";
			if(!empty($column)){
				$params[] = $column;
				$query .= " AND COLUMN_NAME = :3";
			}
			$r = self::query($query, $params);
			return $r->num_rows > 0;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	public static function status($table, $key) {
		if(self::$link === NULL) {self::connect();}
		$r = mysql_query("SHOW TABLE STATUS WHERE Name = '{$table}'");
		if(mysql_errno()){
			throw new Exception(mysql_error(), mysql_errno());
		}
		$row = mysql_fetch_assoc($r);
		return empty($key)?$row:strtolower($row[$key]);
	}

	public static function engine($table) {
		try {
			return self::status($table, 'Engine');
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	public static function version($chk_version = null, $operator = '>=') {
		if(self::$link === NULL) {self::connect();}
		$version = mysql_get_client_info();
		if (empty($chk_version)) {
			return $version;
		} else {
			return version_compare($version, $chk_version, $operator);
		}
	}

	public static function escape($str) {
		return str_replace(
					['\\',"\0","\n","\r","'",'"',"\x1a"],
					['\\\\','\\0','\\n','\\r',"\\'",'\\"','\\Z'],
					$str
				);
		// 연결이 되야 사용 가능하기에 안씀
		// return mysql_real_escape_string($str);
	}

	public static function error() {
		if(mysql_errno()) {
			return new Exception(mysql_error(), mysql_errno());
		} else {
			return false;
		}
	}
}

/* End of file mysql.php */
/* Location: ./db/mysql.php */
