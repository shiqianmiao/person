<?php

/**
 * @brief  model基类
 * @author longwg
 */

require_once dirname(__FILE__) . '/DBMysqli.class.php';
require_once dirname(__FILE__) . '/SqlBuilder.class.php';
//require_once GLOBAL_CONF_PATH . '/cache/MemcacheConfig.class.php';
//require_once FRAMEWORK_PATH . '/util/cache/CacheNamespace.class.php';

abstract class BaseModel {

    // 以下成员变量必须在子类中指定，你懂的！
    protected $dbName = '';
    protected $tableName = '';
    protected $fieldTypes = array();
    protected $dbMasterConfig = array();
    protected $dbSlaveConfig = array();
//    protected $memConfig = array();

    // 基类成员变量
    protected $dbConnMaster;
    protected $dbConnSlave;
    protected $sqlBuilder;
//    public $memHandle;

    public function __construct() {
        $this->init();

        if (empty($this->dbName)) {
            trigger_error('未指定库名', E_USER_ERROR);
        }
        if (empty($this->tableName)) {
            trigger_error('未指定表名', E_USER_ERROR);
        }
        if (empty($this->fieldTypes)) {
            trigger_error('未指定字段类型', E_USER_ERROR);
        }

        $this->sqlBuilder = new SqlBuilder($this->dbName, $this->tableName, $this->fieldTypes);
        $this->sqlBuilder->setDbName($this->dbName);
//        $this->memHandle = CacheNamespace::createCache(CacheNamespace::MODE_MEMCACHE, $this->memConfig);
        //$this->checkAndCreateTable($this->tableName);
    }

    /**
     * @brief 子类实现init方法，类实例化后调用
     * 在子类中实现，必须指定以下变量：
     * this->dbName,
     * $this->dbName,
     * $this->tableName,
     * $this->fieldTypes,
     * $this->dbMasterConfig,
     * $this->dbSlaveConfig
     */
    abstract protected function init();

    protected function getMasterConn() {
        if (!$this->dbConnMaster) {
            if (empty($this->dbMasterConfig)) {
                trigger_error('未指定主库连接配置', E_USER_ERROR);
            }
            $this->dbConnMaster = DBMysqli::createDBHandle($this->dbMasterConfig, $this->dbName);
            if ($this->dbConnMaster === false) {
            	throw new Exception("数据库:{$this->dbName} master 连接失败");
            }
        }
        return $this->dbConnMaster;
    }

    protected function getSlaveConn() {
        if (!$this->dbConnSlave) {
            if (empty($this->dbSlaveConfig)) {
                trigger_error('未指定从库连接配置', E_USER_ERROR);
            }
            $this->dbConnSlave = DBMysqli::createDBHandle($this->dbSlaveConfig, $this->dbName);
            if ($this->dbConnSlave === false) {
            	throw new Exception("数据库:{$this->dbName} slave 连接失败");
            }
        }
        return $this->dbConnSlave;
    }

    public function execute($sql, $fromMaster=false) {
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        $ret = DBMysqli::execute($conn, $sql);
        return $ret;
    }

    public function queryAll($sql, $fromMaster=false) {
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        return DBMysqli::queryAll($conn, $sql);
    }

    public function queryRow($sql, $fromMaster=false) {
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        return DBMysqli::queryRow($conn, $sql);
    }

    public function queryOne($sql, $fromMaster=false) {
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        return DBMysqli::queryOne($conn, $sql);
    }

    public function insert($columnsAndValues) {
        $sql = $this->sqlBuilder->createInsertSql($columnsAndValues);
        $conn = $this->getMasterConn();
        return DBMysqli::insertAndGetID($conn, $sql);
    }

    public function mutilInsert($columnsAndValues) {
        $sql = $this->sqlBuilder->createInsertSql($columnsAndValues);
        $conn = $this->getMasterConn();
        return DBMysqli::execute($conn, $sql);
    }

    public function update($columnsAndValues, $filters) {
        $sql = $this->sqlBuilder->createUpdateSql($columnsAndValues, $filters);
        $conn = $this->getMasterConn();
        if (DBMysqli::execute($conn, $sql)) {
            return DBMysqli::lastAffected($conn);
        }
        return false;
    }

    public function delete($filters) {
        $sql = $this->sqlBuilder->createDeleteSql($filters);
        $conn = $this->getMasterConn();
        if (DBMysqli::execute($conn, $sql)) {
            return DBMysqli::lastAffected($conn);
        }
        return false;
    }

    public function getAll($field = '*', $filters = '', $orderBy = '', $limit = 0, $offset = 0, $fromMaster = false, $useIndex='') {
        $sql = $this->sqlBuilder->createSelectSql($field, $filters, $orderBy, $limit, $offset, $useIndex);
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        return DBMysqli::queryAll($conn , $sql);
    }

    public function getAllFromMaster($field = '*', $filters = '', $orderBy = '', $offset = 0, $limit = 0) {
        return $this->getAll($field, $filters, $orderBy, $offset, $limit, true);
    }

    public function getRow($field = '*', $filters = '', $orderBy = '', $offset = 0, $fromMaster = false) {
        $sql = $this->sqlBuilder->createSelectSql($field, $filters, $orderBy, 1, $offset);
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        return DBMysqli::queryRow($conn , $sql);
    }

    public function getRowFromMaster($field = '*', $filters = '', $orderBy = '', $offset = 0) {
        return $this->getRow($field = '*', $filters, $orderBy, $offset, true);
    }

    public function getOne($field = '*', $filters = '', $fromMaster = false, $useIndex='') {
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        $sql = $this->sqlBuilder->createSelectSql($field, $filters, array(), 0, 0, $useIndex);
        return DBMysqli::queryOne($conn , $sql);
    }

    public function getOneFromMaster($field = '*', $filters = '') {
        return $this->getOne($field, $filters, true);
    }

    public function getByField($fieldName, $fieldValue, $fromMaster = false) {
        $conn = $fromMaster ? $this->getMasterConn() : $this->getSlaveConn();
        $filters = array(array($fieldName, '=', $fieldValue));
        return $this->getRow($filters, '', 0, $fromMaster);
    }

    public function incr($fieldName, $filters, $num = 1) {
        $sql = $this->sqlBuilder->createIncrSql($fieldName, $filters, $num);
        $conn = $this->getMasterConn();
        return DBMysqli::execute($conn, $sql);
    }

    public function decr($fieldName, $filters, $num = 1) {
        $sql = $this->sqlBuilder->createDecrSql($fieldName, $filters, $num);
        $conn = $this->getMasterConn();
        return DBMysqli::execute($conn, $sql);
    }
    
    public function getFields() {
        $conn =$this->getMasterConn();
        $result =  DBMysqli::queryAll($conn , 'SHOW FULL COLUMNS FROM '.$this->tableName);
        $info   =   array();
        if($result) {
            foreach ($result as $key => $val) {
                $val['type'] = preg_replace('/\([^\(\)]+\)\s?\w*/', '', $val['Type']);
                $info[] = $val;
            }
        }
        return $info;
    }

    public function getLastSql() {
        return DBMysqli::getLastSql();
    }
}

