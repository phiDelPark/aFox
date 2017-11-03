<?php
/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

class DB {
	private static $link = NULL;
	private static $options = [];

	private static function connect() {
		$o = self::$options;
		mysqli_report(MYSQLI_REPORT_OFF);
		self::$link = new mysqli(isset($o['host'])   ? $o['host']   : 'localhost',
							 isset($o['user'])   ? $o['user']   : 'root',
							 isset($o['pass'])   ? $o['pass']   : '',
							 isset($o['name'])   ? $o['name'] : 'default',
							 isset($o['port'])   ? $o['port']   : 3306,
							 isset($o['sock'])   ? $o['sock']   : FALSE );
		if( mysqli_connect_errno() ) {
			die(mysqli_connect_error() . ' (' . mysqli_connect_errno() . ')');
		}
		mysqli_query(self::$link, "SET NAMES ".(isset($o['charset']) ? $o['charset'] : "utf8"));
		mysqli_query(self::$link, "SET time_zone = '".(isset($o['time_zone']) ? $o['time_zone'] : "Asia/Seoul")."'");
		mysqli_autocommit(self::$link, TRUE);
	}

	public static function init( /*array*/ $o) {
		self::$options = $o;
	}

	public static function query($query, $params = []) {
		if(self::$link === NULL) {self::connect();}
		if(!is_array($params)) {
			$params = array_slice(func_get_args(), 1);
		}
		if(!empty($params)) {
			$query = preg_replace_callback(
						'/:(\d+)/',
						function ($m) use ($params) {
						  return self::escape($params[$m[1] - 1]);
						}, $query
					);
		}
		$result = mysqli_query(self::$link, $query);
		if(mysqli_errno(self::$link)) {
			throw new Exception(mysqli_error(self::$link), mysqli_errno(self::$link));
		}

		return $result;
	}

	public static function get($query, $params = []) {
		try {
			$r = self::query($query, $params);
			if(!is_object($r)) return [];
			$rset = [];
			while ($row = mysqli_fetch_assoc($r)) {
				$rset[] = $row; break;
			}
			return array_shift($rset);
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function getList($query, $params = [], $callback = NULL) {
		try {
			$r = self::query($query, $params);
			if(!is_object($r)) return [];
			if($callback){
				$rset = $callback($r);
			}else{
				$rset = [];
				while ($row = mysqli_fetch_assoc($r)) {
					$rset[] = $row;
				}
			}
			return $rset;
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function select($table, $wheres, $fields = []) {
		$wheres = implode(' AND ', self::escapeArray($wheres, TRUE));
		$fields = count($fields) > 0 ? implode(',', $fields) : '*';
		try {
			return self::query("SELECT $fields FROM $table WHERE $wheres");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function insert($table, $inserts) {
		$sets = implode( ',', self::escapeArray( $inserts, TRUE ) );
		try {
			return self::query("INSERT INTO $table SET $sets");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function update($table, $updates, $wheres) {
		$sets = implode(',', self::escapeArray( $updates, TRUE ));
		$wheres = implode(' AND ', self::escapeArray( $wheres, TRUE ));
		try {
			return self::query("UPDATE $table SET $sets WHERE $wheres");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function delete($table, $wheres) {
		$wheres = implode(' AND ', self::escapeArray($wheres, TRUE));
		try {
			return self::query("DELETE FROM $table WHERE $wheres");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function count($table, $wheres) {
		$wheres = implode(' AND ', self::escapeArray($wheres, TRUE));
		try {
			$r = self::query("SELECT COUNT(*) as cnt FROM $table WHERE $wheres");
			if(!is_object($r)) return -1;
			$row = mysqli_fetch_assoc($r);
			return $row['cnt'];
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function num($result) {
		return mysqli_num_rows($result);
	}

	public static function row($result) {
		return mysqli_fetch_row($result);
	}

	public static function assoc($result) {
		return mysqli_fetch_assoc($result);
	}

	public static function object($result) {
		return mysqli_fetch_object($result);
	}

	public static function found() {
		$r = self::query('SELECT FOUND_ROWS() AS foundRows');
		$row = mysqli_fetch_assoc($r);
		return $row['foundRows'];
	}

	public static function affected() {
		return mysqli_affected_rows(self::$link);
	}

	public static function insertId() {
		return mysqli_insert_id(self::$link);
	}

	public static function transaction($flags = MYSQLI_TRANS_START_READ_WRITE) {
		if(self::$link === NULL) {self::connect();}
		mysqli_autocommit(self::$link, FALSE);
		return mysqli_begin_transaction(self::$link, $flags);
	}

	public static function commit() {
		$r = mysqli_commit(self::$link);
		mysqli_autocommit(self::$link, TRUE);
		return $r;
	}

	public static function rollback() {
		$r = mysqli_rollback(self::$link);
		mysqli_autocommit(self::$link, TRUE);
		return $r;
	}

	public static function tableExists($table) {
		try {
			$r = self::query(
				"SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :1 AND TABLE_NAME =:2",
				[self::$options['name'], $table]
			);
			return $r->num_rows > 0;
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function columnExists($table, $column) {
		try {
			$r = self::query(
				"SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :1 AND TABLE_NAME =:2 AND COLUMN_NAME = :3",
				[self::$options['name'], $table, $column]
			);
			return $r->num_rows > 0;
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function error() {
		if(mysqli_errno(self::$link)) {
			return new Exception(mysqli_error(self::$link), mysqli_errno(self::$link));
		} else {
			return FALSE;
		}
	}

	public static function status($table, $key) {
		if(self::$link === NULL) {self::connect();}
		$r = mysqli_query(self::$link, "SHOW TABLE STATUS WHERE Name = '{$table}'");
		if(mysqli_errno(self::$link)) {
			throw new Exception(mysqli_error(self::$link), mysqli_errno(self::$link));
		}
		$row = mysqli_fetch_assoc($r);
		return empty($key)?$row:strtolower($row[$key]);
	}

	public static function engine($table) {
		return self::status($table, 'Engine');
	}

	public static function version() {
		if(self::$link === NULL) {self::connect();}
		return mysqli_get_client_version(self::$link);
	}

	public static function escape($s) {
		if($s === TRUE || $s === FALSE) { return (int)$s; }
		else if(is_int($s) || is_float($s)) { return $s; }
		else { return "'".str_replace(['\\',"\0","\n","\r","'",'"',"\x1a"],['\\\\','\\0','\\n','\\r',"\\'",'\\"','\\Z'],$s)."'"; }
		//연결이 안되있을때도 동작하기 위해 str_replace 사용
		//if(self::$link === NULL) {self::connect();}
		//return "'".mysqli_real_escape_string(self::$link, $s)."'";
	}

	public static function escapeArray(&$fields, $useKeys = FALSE) {
		$r = [];
		foreach($fields as $key => &$val) {
			if(is_null($val)) continue;
			if(is_array($val) && ($key == 'AND' || $key == 'OR')) {
				$tmp = self::escapeArray($val, $useKeys);
				if(count($tmp) > 0) $r[] = '('.implode(' '.$key.' ', $tmp).')';
			} else {
				$operation = '=';
				//(=|<>|<=|>=|<|>|IN|LIKE|IS)
				if($useKeys && preg_match("/(.+){(=|<>|<=|>=|<|>|[A-Z]+)}$/", $key, $matches)) {
					$key = $matches[1];
					$operation = $matches[2];
					if($operation == 'IS') { $key = '('.$key.')'; } // value = ('null' or 'not null')
					else if($operation == 'IN') {
						$key = '('.$key.')';
						$val = explode(',', $val);
						if(count($val)===0 || empty($val[0])) continue;
						foreach ($val as $k=>$v) $val[$k] = self::escape($v);
						$val = '('.implode(',', $val).')';
					}
				}
				if($q_skip = preg_match("/^\((.+?)\)$/", $key, $matches)) {
					$key = $matches[1];
				}
				$r[] = ($useKeys ? "`$key` $operation ":'') . ($q_skip ? $val : self::escape($val));
			}
		}
		return $r;
	}

	// deprecated
	public static function quotes($s) {
		return self::escape($s);
	}
	// deprecated
	public static function quotesArray(&$fields, $useKeys = FALSE) {
		return self::escapeArray($fields, $useKeys);
	}
}

/* End of file mysql.php */
/* Location: ./db/mysql.php */
