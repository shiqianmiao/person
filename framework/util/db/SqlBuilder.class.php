<?php

/**
 * @brief  生成sql语句的方法，只适用于一个数据表的操作，不适合联表查询
 * @author longwg
 */

class SqlBuilder {

    // 默认的边界符
    const MODIFIER_DEFAULT = "'"; 
    
    // update中set时的关键字以及分割符
    private static $_hashSet = array(
        ' SET ', 
        ', '
    );

    // update中set时的关键字以及分割符
    private static $_hashWhere = array(
        ' WHERE ', 
        ' AND '
    );

    // where时的关键字以及分割符
    private static $_noModType = array( ///< 不用分割符号的数据类型(全大写)，如：INT
        'TINYINT',
        'SMALLINT',
        'MEDIUMINT',
        'INT',
        'INTEGER',
        'BIGINT',
        'FLOAT',
        'DOUBLE',
        'DECIMAL',
    );

    // 你懂得
    protected $dbName = '';
    protected $tableName = '';
    protected $fieldTypes = array();
    
    public function __construct($dbName, $tableName, $fieldTypes) {
        $this->dbName = $dbName;
        $this->tableName = $tableName;
        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @brief 取得数据库名
     * @return string
     */
    public function getDbName() {
        return $this->dbName;
    }

    public function setDbName($dbName) {
        $this->dbName = $dbName;
    }

    /**
     * @brief 取得数据表名
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * @brief 检查字段的类型，确定要不要用边界符
     * @param String $fieldName
     * @return boolean
     */
    private function _getFieldTypeMod($fieldName) {
        $pos = strrpos($fieldName, '.');
        if (false !== $pos) {
            $fieldName = substr($fieldName, $pos + 1);
        }
        if (isset($this->fieldTypes[$fieldName]) && in_array(strtoupper($this->fieldTypes[$fieldName]), self::$_noModType)) {
            return '';
        }
        return self::MODIFIER_DEFAULT;
    }

    /**
     * @brief 可以获取一个表的数据。
     * @param String $field 字段
     * @param String or Array $where 条件
     * @param int $limit 结果集每页纪录数
     * @param int $offset 结果集的偏移
     * @return String SQL
     */
    public function createSelectSql($field = '*', $where = array(), $orderBy = array(), $limit = 0, $offset = 0, $useIndex='') {
        if ($limit > 0) {
            $offset = (int) $offset;
            $limit = " LIMIT {$offset}, $limit";
        } else {
            $limit = '';
        }
        return 'SELECT ' . $field . ' FROM ' . $this->_checkTableName() . $useIndex . $this->createWhereSql($where) . $this->createOrderbySql($orderBy) . $limit;
    
    }

    /**
     * @brief 数据插入
     * @param Array $data 要插入的数据
     * @return String sql
     */
    public function createInsertSql($data) {
        if (isset($data[0]) && is_array($data[0])) {
            $insertValues = array();
            foreach ($data as $item) {
                $this->filterFields($item);
                $arrKeys = array_keys($item);
                $arrVals = array();
                foreach ($arrKeys as $f) {
                    $mod = $this->_checkEmptyValue($f, $item[$f]);
                    if (false === $mod) {
                        unset($arrKeys[array_search($f, $arrKeys)]);
                        continue;
                    }
                    $arrVals[] = $mod . addslashes($item[$f]) . $mod;
                }
                $field  = implode('`, `', $arrKeys);
                $values = implode(', ', $arrVals);
                $values = '(' . $values . ')';
                $insertValues[] = $values;
            }
            if (!empty($insertValues)) {
                $insertValues = implode(',', $insertValues);
                return 'INSERT INTO ' . $this->_checkTableName() . ' (`' . $field . '`) VALUES ' . $insertValues;
            }
        } else {
            $this->filterFields($data);
            $arrKeys = array_keys($data);
            $arrVals = array();
            foreach ($arrKeys as $f) {
                $mod = $this->_checkEmptyValue($f, $data[$f]);
                if (false === $mod) {
                    unset($arrKeys[array_search($f, $arrKeys)]);
                    continue;
                }
                $arrVals[] = $mod . addslashes($data[$f]) . $mod;
            }
            $field = implode('`, `', $arrKeys);
            $values = implode(', ', $arrVals);
            return 'INSERT INTO ' . $this->_checkTableName() . ' (`' . $field . '`) VALUES (' . $values . ')';
        }
    }

    /**
     * @brief 数据更新
     * @param String or Array $setData 要插入的数据
     * @param String or Array $where 条件
     * @return String sql
     */
    public function createUpdateSql($setData, $where) {
        return 'UPDATE ' . $this->_checkTableName() . $this->createSetSql($setData, true) . $this->createWhereSql($where);
    }

    /**
     * @brief 更新字段数据增量 update XXX set xxx=xxx+1 where ....
     * @param type $fieldName
     * @param type $where
     * @param type $num
     * @return type string
     */
    public function createIncrSql($fieldName, $where, $num = 1) {
        $fieldName = $this->_checkField($fieldName);
        $num = (int) $num;
        return 'UPDATE ' . $this->_checkTableName() . " SET {$fieldName} = {$fieldName} + {$num} " . $this->createWhereSql($where);
    }

    /**
     * @brief 更新字段数据减量 update XXX set xxx=xxx-1 where ....
     * @param type $fieldName
     * @param type $where
     * @param type $num
     * @return type string
     */
    public function createDecrSql($fieldName, $where, $num = 1) {
        $fieldName = $this->_checkField($fieldName);
        $num = (int) $num;
        return 'UPDATE ' . $this->_checkTableName() . " SET {$fieldName} = {$fieldName} - {$num} " . $this->createWhereSql($where);
    }

    /**
     * @brief 删除数据
     * @param $where WHERE
     * @return String sql
     */
    public function createDeleteSql($where) {
        return "DELETE FROM " . $this->_checkTableName() . $this->createWhereSql($where);
    }

    /**
     * @brief 生成Where数据集
     * @param String or Array $filters
     */
    public function createWhereSql($filters) {
        $ret = $this->_createWhereSql($filters, 'AND');
        if ($ret) {
            return " WHERE " . $ret;
        }
        return '';
    }

    private function _createWhereSql($filters, $andOr = 'AND') {
        if (empty($filters)) {
            return '';
        }

        $ret = array();
        foreach ($filters as $filter) {
            if (is_array($filter)) {
                if ($filter[0]) {
                    $operator = strtolower($filter[1]);
                    $mod = $this->_getFieldTypeMod($filter[0]);
                    $fieldName = $this->_checkField($filter[0]);
                    if ($operator == 'in') {
                        if (is_array($filter[2]) && count($filter[2]) > 0) {
                            $inArr = array();
                            foreach ($filter[2] as $val) {
                                $inArr[] = $mod . addslashes($val) . $mod;
                            }
                            $ret[] = $fieldName . ' IN (' . implode(',', $inArr) . ')';
                        }
                    } else if ($operator == 'not in') {
                        if (is_array($filter[2]) && count($filter[2]) > 0) {
                            $inArr = array();
                            foreach ($filter[2] as $val) {
                                $inArr[] = $mod . addslashes($val) . $mod;
                            }
                            $ret[] = $fieldName . ' NOT IN (' . implode(',', $inArr) . ')';
                        }
                    } else if ($operator == 'like') {
                        $ret[] = $fieldName . ' LIKE ' . $mod . addslashes($filter[2]) . $mod;
                    } else {
                        $ret[] = "{$fieldName} {$filter[1]} {$mod}" . addslashes($filter[2]) . "{$mod}";
                    }
                } else if (isset($filter['or']) || isset($filter['OR'])) {
                    if (isset($filter['or'])) {
                        $_filter = $filter['or'];
                    } else if (isset($filter['OR'])) {
                        $_filter = $filter['OR'];
                    }
                    $_ret = $this->_createWhereSql($_filter, 'OR');
                    if ($_ret) {
                        $ret[] = $_ret;
                    }
                
                } else {
                    continue;
                }
            } else if (is_string($filter)) {// 直接写sql
                $ret[] = $filter;
            } else {
                continue;
            }
        }
        
        if (count($ret) > 0) {
            return '(' . implode(" {$andOr} ", $ret) . ')';
        }
        
        return '';
    }

    public function createOrderbySql($orderBys) {
        if (empty($orderBys) || !is_array($orderBys)) {
            return '';
        }
        $ret = array();
        foreach ($orderBys as $key => $val) {
            if (empty($key)) {
                continue;
            }
            $key = $this->_checkField($key);
            $val = strtoupper($val) == 'DESC' ? 'DESC' : 'ASC';
            $ret[] = "$key $val";
        }
        
        $sql = '';
        if (count($ret) > 0) {
            $sql = " ORDER BY  " . implode(", ", $ret);
        }
        
        return $sql;
    }

    /**
     * @brief 生成Set数据集
     * @param String or Array $data
     */
    protected function createSetSql($data) {
        return $this->_parseHash($data, self::$_hashSet);
    }

    /**
     * @brief 检查表名
     * @return String
     */
    protected function _checkTableName() {
        return "`" . $this->dbName . "`.`" . $this->tableName . "`";
    }

    /**
     * @brief 过滤没用的字段
     * @param Array $fields
     */
    protected function filterFields(&$data) {
        $fields = array_keys($data);
        foreach ($fields as $f) {
            if (! array_key_exists($f, $this->fieldTypes)) {
                unset($data[$f]);
            }
        }
    }

    /**
     * @brief 将数组数据生成键值对形式的字符串
     * @param String or Array $data
     * @param Array $ws Where 和 Set时的关键字和分割符
     * @return String
     */
    private function _parseHash($data, $ws, $filter = true) {
        
        $strHash = '';
        if (is_string($data)) {
            $strHash = $data;
        } else if (is_array($data)) {
            if ($filter) {
                $this->filterFields($data);
            }
            
            $arrHash = array();
            foreach ($data as $k => $v) {
                $mod = $this->_checkEmptyValue($k, $v);
                
                if ($filter && $mod === false) {
                    continue;
                }
                
                $arrHash[] = $this->_checkField($k) . "=" . $mod . addslashes($v) . $mod;
            }
            $strHash = implode($ws[1], $arrHash);
        }
        return $strHash ? " {$ws[0]} {$strHash}" : ' ';
    }

    /**
     * @brief 检查字段名，过滤掉特殊字符（不包含.） 得到合法的字符
     * @param String $field
     */
    private function _checkField($field) {
        $field = preg_replace('/[^\w\.\+]/', '', $field);
        $pos = strpos($field, '.');
        $posPlus = strpos($field, '+');
        if ($pos > 0) {
            $field = str_replace('.', "`.`", $field);
        } else if ($pos === 0) {
            $field = substr($field, 1);
        }
        if ($posPlus > 0) {
            $field = str_replace('+', "`+`", $field);
        } else if ($posPlus === 0) {
            $field = substr($field, 1);
        }
        
        return "`" . $field . "`";
    }

    /**
     * 
     * @brief 检查不使用边界符时的数据是否合法（要为数字）
     * @param String $field 字段
     * @param String $val	字段值
     * @return boolean or char
     */
    private function _checkEmptyValue($field, $val) {
        $mod = $this->_getFieldTypeMod($field);
        
        if (empty($mod) && ! preg_match('/\d+/', $val)) {
            return false;
        }
        return $mod;
    }
}
