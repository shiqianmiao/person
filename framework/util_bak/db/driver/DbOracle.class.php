<?php
/**
 * Oracle 数据库驱动类
 *
 * @author    戴志君 <dzjzmj@gmail.com>
 * @since     2013-3-5
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc
 */


define('OCI_RETURN_ALLS',OCI_BOTH + OCI_RETURN_LOBS);


class DbOracle {
	/**
	 * select方法返回的最大记录数
	 */
	const MAX_ROW_NUM = 2000;

	/**
	 * 数据查询结果集对象
	 * @var object $dataSet
	 */
	public $dataSet			= NULL ;

	/**
	 * 数据源对象
	 * @var object $ds
	 */
	public $ds				= NULL ;

	/**
	 * 查询的SQL语句
	 * @var string $sql
	 */
	public $sql				= '' ;

	/**
	 * 执行查询的模式，值为 OCI_COMMIT_ON_SUCCESS 或 OCI_DEFAULT
	 * @var string $excuteMode
	 */
	public $executeMode	= OCI_COMMIT_ON_SUCCESS ;

	private $queueModel = false;

    static $DB_MASTER = array();

    static $DB_SLAVE  = array();

    private $_master_connect = null;

    private $_slave_connect = null;

     /**
     * 构造函数
     * @param object $ds 数据库
     * @param string $sql 要初始化查询的SQL语句
     */
    function __construct($masterConfig, $slaveConfig) {
        if (!function_exists('oci_connect')) {
            exit('oci8 extension no support');
        }
        self::$DB_MASTER = $masterConfig;
        self::$DB_SLAVE = $slaveConfig;
    }

    function connect($type = 'master') {
        if ('master'==$type || ('slave'==$type && empty(self::$DB_SLAVE))) {
            if ($this->_master_connect===null) {
                $this->_master_connect = @oci_connect(self::$DB_MASTER['db_user'], self::$DB_MASTER['db_password'], self::$DB_MASTER['db_name'], self::$DB_MASTER['db_charset']);
                if (!$this->_master_connect) {
                    $errorMessage = '连接数据库服务器' . self::$DB_MASTER['db_name'] . '失败:'. oci_error();
                    throw new DbException($errorMessage, DbException::DB_OPEN_FAILED);
                }

            }
            return $this->_master_connect;
        } else {
            if ($this->_slave_connect===null) {
                $this->_slave_connect = @oci_connect(self::$DB_SLAVE['db_user'], self::$DB_SLAVE['db_password'], self::$DB_SLAVE['db_name'], self::$DB_SLAVE['db_charset']);
                if (!$this->_slave_connect) {
                    $errorMessage = '连接数据库服务器' . self::$DB_MASTER['db_name'] . '失败:'. oci_error();
                    throw new DbException($errorMessage, DbException::DB_OPEN_FAILED);
                }

            }
            return $this->_slave_connect;
        }
    }

    /**
	 * 释放所占用的内存
	 * @param object $dataSet 需要释放资源的结果集
	 * @access public
	 */
	public function close($dataSet=NULL) {
		if ($dataSet) {
			@oci_free_statement($dataSet);
		} else {
			@oci_free_statement($this->dataSet);
			$this->eof = false ;
			$this->recordCount = 0 ;
			$this->recNo = -1 ;
		}

	}

	function closeConnect() {
		@oci_close($this->_master_connect);
        @oci_close($this->_slave_connect);
	}
	/**
	 * 对$pass进行数据库加密,返回加密之后的值
	 * @param string $pass 要加密的字符串
	 * @return string
	 * @access public
	 */
	public function encodePassword($pass) {
		return md5($pass);
	}

	/**
	 * 得到错误信息和错误代号
	 * @param integer $queryResult 查询结果
	 * @return array
	 * @access protected
	 */
	protected function errorInfo($conn) {
		$result = oci_error($conn);
		return $result;
	}

	/**
	 * 错误信息处理
	 * @param string $errorId 错误ID
	 * @param string $errorMessage 错误信息
	 * @access protected
	 */
	protected function error($errorId, $errorMessage) {
		throw new DbException($errorMessage, $errorId);
	}


	/**
	 * 执行SQL语句
	 * @param string $sql SQL语句
	 * @return object
	 * @param int $rowFrom 启始行号，行号从1开始
	 * @param int $rowTo 结束行号，值为0表示
	 * @access public
	 * @see DbQueryForOracle::open
	 */
	private function _execute($type,$sql = '') {
        $conn = $this->connect($type);
		$dataSet = @oci_parse($conn, $sql);
		$executeSucceed = @oci_execute($dataSet, $this->executeMode);
		if (!$dataSet || !$executeSucceed) {
			$sqlError = $this->errorInfo($conn);
			$errorMessage = '执行[ SQL ]出错！  ['
					. $sql . ']: '. $sqlError['message'];
			$this->error(DbException::DB_QUERY_ERROR, $errorMessage);
		}
		return $dataSet;
	}

	/**
	 * 执行查询操作  select
	 * @param string $sql SQL语句
	 * @return object
	 * @param int $rowFrom 启始行号，行号从1开始
	 * @param int $rowTo 结束行号，值为0表示
	 * @access public
	 * @see DbOracle::open
	 */
	public function query($sql = '', $rowFrom = 0, $rowTo = self::MAX_ROW_NUM) {
		if ($rowFrom != 0 && $rowTo != self::MAX_ROW_NUM) {
			$sql = 'select * from (select row_.*, rownum rownum_ from ('
					. $sql . ') row_ where rownum <= '
					. $rowTo . ') where rownum_ >= ' . $rowFrom;
		}
		return $this->_execute('slave', $sql);
	}

	/**
	 * 执行写操作  update insert delete
	 * @param string $sql SQL语句
	 * @return object
	 * @access public
	 */
	public function execute($sql = '') {

		return $this->_execute('master', $sql);
	}
    /**
	 * 执行SQL语句，结果集保存到属性$dataSet中
	 * @param string $sql SQL语句
	 * @param int $rowFrom 启始行号，行号从1开始
	 * @param int $rowTo 结束行号，值为0表示
	 * @return object
	 * @access public
	 * @see DbQueryForOracle::execute
	 */
	public function open($sql='', $rowFrom = 0, $rowTo = self::MAX_ROW_NUM) {
		$this->dataSet = $this->query($sql, $rowFrom, $rowTo);
		$this->sql = $sql ;
		return $this->dataSet;
	}

	/**
	 * 将一行的各字段值拆分到一个数组中
	 * @param object $dataSet 结果集
	 * @param integer $resultType 返回类型，OCI_ASSOC、OCI_NUM 或 OCI_BOTH
	 * @return array
	 */
	public function fetchRecord($dataSet=NULL, $resultType=OCI_ASSOC) {
		$result = @oci_fetch_array(($dataSet) ? $dataSet : $this->dataSet, $resultType);
		if (is_array($result)) {
			foreach ($result as $key => $value) {
				if (!is_numeric($key)) {
					$result[strtolower($key)] = $value;
                    unset($result[$key]);
				}
			}
		}
		return $result;
	}

	/**
	 * 取得字段数量
	 * @param object $dataSet 结果集
	 * @return integer
	 */
	public function getFieldCount($dataSet = NULL) {
		return oci_num_fields(($dataSet) ? $dataSet : $this->dataSet);
	}

	/**
	 * 取得下一条记录。返回记录号，如果到了记录尾，则返回FALSE
	 * @return integer
	 * @access public
	 * @see getPrior()
	 */
	public function next() {
		return $this->fetchRecord();
	}

	/**
	 * 得到当前数据库时间，格式为：yyyy-mm-dd hh:mm:ss
	 * @return string
	 * @access public
	 */
	public function getNow() {
		return $this->getValue('SELECT TO_CHAR(SYSDATE, \'YYYY-MM-DD HH24:MI:SS\') dateOfNow FROM DUAL');
	}

	/**
	 * 根据SQL语句从数据表中取数据，只取第一条记录的值，
	 * 如果记录中只有一个字段，则只返回字段值。
	 * 未找到返回 FALSE
	 *
	 * @param string $sql SQL语句
	 * @return array
	 * @access public
	 */
	public function getOne($sql = '', $hasClob = false) {
		$dataSet = $this->query($sql, 1, 1);
		if ($hasClob) {
			$returnType = OCI_RETURN_ALLS;
		} else {
			$returnType = OCI_ASSOC;
		}
		if ($result = $this->fetchRecord($dataSet,$returnType)) {
			$fieldCount = $this->getFieldCount($dataSet);
			$this->close($dataSet);
			return ($fieldCount<=2) ? array_shift($result) : $result;
		} else {
			return false ;
		}
	}
	/**
	 * 取序列值
	 *
	 * @param string $seqName
	 * @return int
	 */
	public function getSeq($seqName) {
		$dataSet = $this->execute('SELECT '.$seqName.'.nextval from sys.dual');
		if ($result = $this->fetchRecord($dataSet)) {
			$this->close($dataSet);
			return current($result);
		} else {
			return false ;
		}
	}

	/**
	 * 开始事务
	 * @access public
	 */
	public function begin() {
		$this->executeMode = OCI_DEFAULT;
	}

	/**
	 * 提交事务
	 * @access public
	 */
	public function commit() {
		oci_commit($this->ds->connect);
		$this->executeMode = OCI_COMMIT_ON_SUCCESS;
	}

	/**
	 * 回滚事务
	 * @access public
	 */
	public function rollback() {
		oci_rollback($this->ds->connect);
		$this->executeMode = OCI_COMMIT_ON_SUCCESS;
	}

	/**
	 * 插入一条记录
	 * @param string $tableName 表名
	 * @param array $fieldArray 字段数组
	 * @param string $whereForUnique 唯一性条件
	 * @param string $clobField clob类型的字段
	 * @return int
	 * @access public
	 */
	public function insert($tableName, $fieldArray, $whereForUnique = NULL, $clobField = NULL ) {
		if (!$tableName || !$fieldArray || !is_array($fieldArray)) {
			throw new Exception('参数 $tableName 或 $fieldArray 的值不合法！');
		}
		if ($whereForUnique) {
			$where = ' WHERE ' . $whereForUnique;
			$isExisted = $this->getValue('SELECT COUNT(*) FROM ' . $tableName . $where);
			if ($isExisted) {
				throw new DbException('记录已经存在！', DbException::DB_RECORD_IS_EXISTED);
			}
		}
		$fieldNameList = array();
		$fieldValueList = array();
		foreach ($fieldArray as $fieldName => $fieldValue) {
			if (!is_int($fieldName)) {
				$fieldNameList[] = $fieldName;
				if ($clobField && $clobField==$fieldName) {
					$fieldValueList[] = 'EMPTY_CLOB()';
					$hasClob = true;
					$clobStr = str_replace("''","'",$fieldValue);
				} else {
					if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/',$fieldValue)) {
						$fieldValueList[] = 'to_date(\''.$fieldValue.'\',\'YYYY-MM-DD\')';
					} elseif (preg_match('/^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}$/',$fieldValue)) {
						$fieldValueList[] = 'to_date(\''.$fieldValue.'\',\'YYYY-MM-DD hh24:mi:ss\')';
					} else {
						$fieldValueList[] = '\'' . $fieldValue . '\'';
					}
				}
			}
		}
		$fieldName = implode(',', $fieldNameList);
		$fieldValue = implode(',', $fieldValueList);
		$sql = 'INSERT INTO ' . $tableName . '('
					. $fieldName . ') VALUES (' . $fieldValue . ')';
		if ($hasClob) {
			$sql .= ' RETURNING content INTO :clob_string';
			$dataSet = oci_parse($this->ds->connect, $sql);
			$clob = oci_new_descriptor($this->ds->connect, OCI_D_LOB);
			oci_bind_by_name($dataSet, ':clob_string', $clob, -1, OCI_B_CLOB);
			oci_execute($dataSet, OCI_DEFAULT);
			$clob->save($clobStr);
			oci_commit($this->ds->connect);
			return $dataSet;
		} else {
			return $this->execute($sql);
		}
	}

	/**
	 * 更新一条记录
	 * @param string $tableName 表名
	 * @param array $fieldArray 字段数组
	 * @param string $whereForUpdate 查询条件
	 * @param string $whereForUnique 唯一性条件
	 * @param string $clobField clob类型的字段
	 * @return int
	 * @access public
	 */
	public function update($tableName, $fieldArray, $whereForUpdate=NULL, $whereForUnique=NULL, $clobField = NULL) {
		if (!$tableName || !$fieldArray || !is_array($fieldArray)) {
			throw new Exception('参数 $tableName 或 $fieldArray 的值不合法！');
		}
		if ($whereForUnique) {
			$where = ' WHERE ' . $whereForUnique;
			$isExisted = $this->getValue('SELECT COUNT(*) FROM ' . $tableName . $where);
			if ($isExisted) {
				throw new DbException('记录已经存在！', DbException::DB_RECORD_IS_EXISTED);
			}
		}
		$fieldNameValueList = array();
		foreach ($fieldArray as $fieldName => $fieldValue) {
			if (!is_int($fieldName)) {
				if ($clobField && $clobField==$fieldName) {
					$fieldNameValueList[] = $fieldName . '=EMPTY_CLOB()';
					$hasClob = true;
					$clobStr = str_replace("''","'",$fieldValue);
				} else {
					if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/',$fieldValue)) {
						$fieldNameValueList[] = $fieldName . '=to_date(\''.$fieldValue.'\',\'YYYY-MM-DD\')';
					} elseif (preg_match('/^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}$/',$fieldValue)) {
						$fieldNameValueList[] = $fieldName . '=to_date(\''.$fieldValue.'\',\'YYYY-MM-DD hh24:mi:ss\')';
					} else {
						$fieldNameValueList[] = $fieldName . '=\'' . $fieldValue . '\'';
					}

				}
			}
		}
		$fieldNameValue = implode(',', $fieldNameValueList);
		if ($whereForUpdate) {
			$whereForUpdate = ' WHERE ' . $whereForUpdate;
		}
		$sql = 'UPDATE ' . $tableName
				. ' SET ' . $fieldNameValue . $whereForUpdate;
		if ($hasClob) { // 对 clob 字段处理
			$sql .= ' RETURNING content INTO :clob_string';
			$dataSet = oci_parse($this->ds->connect, $sql);
			$clob = oci_new_descriptor($this->ds->connect, OCI_D_LOB);
			oci_bind_by_name($dataSet, ':clob_string', $clob, -1, OCI_B_CLOB);
			oci_execute($dataSet, OCI_DEFAULT);
			$clob->save($clobStr);
			oci_commit($this->ds->connect);
			return $dataSet;
		} else {
			return $this->execute($sql);
		}
	}

	/**
	 * 选择一条记录
	 * @param string $sql sql语句
	 * @param string $dataFormat 返回数据格式, 值有"array","hashmap","hashmap_str","dataset"
	 * @param int $rowFrom 启始行号，行号从1开始
	 * @param int $rowTo 结束行号，值为0表示
	 * @result array
	 * @access public
	 */
	public function getAll($sql, $dataFormat = 'array', $rowFrom = 0, $rowTo = self::MAX_ROW_NUM, $hasClob = false) {
		$dataSet = $this->query($sql, $rowFrom, $rowTo);
		switch ($dataFormat) {
		case 'array': //数组
            $result = array();
            if (oci_fetch_all($dataSet, $result, 0, -1, OCI_FETCHSTATEMENT_BY_ROW)){
                foreach ($result as $k => $v) {
                    $result[$k] = array_change_key_case($result[$k], CASE_LOWER);
                }
            }
            break;
			$result = array();
			$isMultiField = ($this->getFieldCount($dataSet) > 1);
			$i = 0;
			if ($hasClob) {
				$returnType = OCI_RETURN_ALLS;
			} else {
				$returnType = OCI_BOTH;
			}
			while ($data = $this->fetchRecord($dataSet,$returnType)) {
				$result[$i] = ($isMultiField) ? $data : $data[0];
				$i++;
			}
			$this->close($dataSet);
			break;

		case 'hashmap': //散列表
			$result = array();
			while ($data = $this->fetchRecord($dataSet)) {
				$result[ $data[0] ] = $data[1];
			}
			$this->close($dataSet);
			break;

		case 'hashmap_str': //散列表字符串
			$result = array();
			while ($data = $this->fetchRecord($dataSet, OCI_NUM)) {
				$result[] = $data[0] . '=' . $data[1];
			}
			$result = implode('|', $result);
			$this->close($dataSet);
			break;

		default: //dataset 数据集，当返回数据格式为数据集时，select方法的功能与execute方法相同
			$result = $dataSet;
		}
		return $result;
	}

	/**
	 * 返回最大值
	 * @param string $tableName 表名
	 * @param string $idField 字段名
	 * @param string $where 查询条件
	 * @return int
	 * @access public
	 */
	public function getMax($tableName, $idField, $where = NULL) {
		$where = ($where) ? (' WHERE ' . $where) : '';
		return $this->getOne('SELECT MAX(' . $idField . ') FROM ' . $tableName . $where);
	}
}