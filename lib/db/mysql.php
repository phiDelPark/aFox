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
						  return self::quotes($params[$m[1] - 1]);
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

	public static function select($table, $wheres) {
		$wheres = implode(' AND ', self::quotesArray($wheres, TRUE));
		try {
			return self::query("SELECT * FROM $table WHERE $wheres");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function insert($table, $inserts) {
		$sets = implode( ',', self::quotesArray( $inserts, TRUE ) );
		try {
			return self::query("INSERT INTO $table SET $sets");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function update( $table, $updates, $wheres) {
		$sets = implode(',', self::quotesArray( $updates, TRUE ));
		$wheres = implode(' AND ', self::quotesArray( $wheres, TRUE ));
		try {
			return self::query("UPDATE $table SET $sets WHERE $wheres");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function delete($table, $wheres) {
		$wheres = implode(' AND ', self::quotesArray($wheres, TRUE));
		try {
			return self::query("DELETE FROM $table WHERE $wheres");
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
	}

	public static function count($table, $wheres) {
		$wheres = implode(' AND ', self::quotesArray($wheres, TRUE));
		try {
			$r = self::query("SELECT COUNT(*) as cnt FROM $table WHERE $wheres");
			if(!is_object($r)) return -1;
			$row = mysqli_fetch_assoc($r);
			return $row['cnt'];
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage(), $ex->getCode());
		}
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

	public static function num() {
		return mysql_num_rows(self::$link);
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

	public static function error() {
		if(mysqli_errno(self::$link)) {
			return new Exception(mysqli_error(self::$link), mysqli_errno(self::$link));
		} else {
			return FALSE;
		}
	}

	public static function version() {
		if(self::$link === NULL) {self::connect();}
		return mysqli_get_client_version(self::$link);
	}

	public static function quotes($s) {
		if($s === TRUE || $s === FALSE) { return (int)$s; }
		else if(is_int($s) || is_float($s)) { return $s; }
		else { return "'".str_replace(['\\',"\0","\n","\r","'",'"',"\x1a"],['\\\\','\\0','\\n','\\r',"\\'",'\\"','\\Z'],$s)."'"; }
		//연결이 안되있을때도 동작하기 위해 str_replace 사용
		//if(self::$link === NULL) {self::connect();}
		//return "'".mysqli_real_escape_string(self::$link, $s)."'";
	}

	public static function quotesArray(&$fields, $useKeys = FALSE) {
		$r = [];
		foreach($fields as $key => &$val) {
			if(is_null($val)) continue;
			if(is_array($val) && ($key == 'AND' || $key == 'OR')) {
				$tmp = self::quotesArray($val, $useKeys);
				if(count($tmp) > 0) $r[] = '('.implode(' '.$key.' ', $tmp).')';
			} else {
				$operation = '=';
				if($useKeys && preg_match("/(.+){(=|<>|<=|>=|<|>|IN|LIKE|IS)}$/", $key, $matches)) {
					$key = $matches[1];
					$operation = $matches[2];
					if($operation == 'IS') { $key = '('.$key.')'; } // value = ('null' or 'not null')
					else if($operation == 'IN') {
						$key = '('.$key.')';
						$val = explode(',', $val);
						if(count($val)===0 || empty($val[0])) continue;
						foreach ($val as $k=>$v) $val[$k] = self::quotes($v);
						$val = '('.implode(',', $val).')';
					}
				}
				if($q_skip = preg_match("/^\((.+?)\)$/", $key, $matches)) {
					$key = $matches[1];
				}
				$r[] = ($useKeys ? "`$key` $operation ":'') . ($q_skip ? $val : self::quotes($val));
			}
		}
		return $r;
	}
}

/* End of file mysql.php */
/* Location: ./db/mysql.php */