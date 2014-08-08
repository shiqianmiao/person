<?php

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

/**
 * @author aozhongxu
 */

class SsoUserModel extends BaseModel {
    
    protected function init($id = '') {
        $this->dbName         = DBConfig::DB_MBS;
        $this->tableName      = 'mbs_user'; 
        $this->dbMasterConfig = DBConfig::$SERVER_MASTER; 
        $this->dbSlaveConfig  = DBConfig::$SERVER_SLAVE; 
        $this->fieldTypes     = array(
            'id'         => 'INT',
            'username'   => 'VARCHAR',
            'passwd'     => 'VARCHAR',
            'real_name'  => 'VARCHAR',
            'sex'        => 'INT',
            'email'      => 'VARCHAR',
            'mobile'     => 'BIGINT',
            'role_id'    => 'INT',
            'dept_id'    => 'INT',
            'inert_time' => 'INT',
            'status'     => 'INT',
            'birth_day'   => 'INT',
            'modify_pwd'  => 'INT',
            'login_count' => 'INT',
            'last_login_time' => 'INT',
        );
    }

    /**
     * 根据用户账号 获取真实名字
     * @param <type> $account  用户账号
     * @return <type> 真实名字
     */
    public function getNameForAccount($account){
        return $this->getOne('real_name', array(array('username', '=', $account)));
    }
    public function getRow($field = '*', $filters = '', $orderBy = '', $offset = 0, $fromMaster = false) {
        $ret = parent::getRow('*', $filters, $orderBy, $offset, $fromMaster);
        if (empty($ret)) {
            return array();
        }
        $ret['account'] = $ret['username'];
        $ret['password'] = $ret['passwd'];
        $ret['name'] = $ret['real_name'];
        $ret['phone'] = $ret['mobile'];
        $ret['role'] = $ret['role_id'];
        $ret['department'] = $ret['dept_id'];
        $ret['leader'] = '';
        $ret['entry_time'] = 0;
        $ret['in_time'] = $ret['inert_time'];
        $ret['forbidden'] = $ret['status'] == 1 ? 0 : 1;
        $ret['birthday'] = $ret['birth_day'];
        $ret['is_modify'] = $ret['modify_pwd'];
        $ret['last_login'] = $ret['last_login_time'];
        return $ret;
    }
    public function getAll($field = '*', $filters = '', $orderBy = '', $limit = 0, $offset = 0, $fromMaster = false) {
        $retArray = parent::getAll('*', $filters, $orderBy, $limit, $offset, $fromMaster);
        if (empty($retArray)) {
            return array();
        }
        $retAll = array();
        foreach ($retArray as $ret) {
            $ret['account'] = $ret['username'];
            $ret['password'] = $ret['passwd'];
            $ret['name'] = $ret['real_name'];
            $ret['phone'] = $ret['mobile'];
            $ret['role'] = $ret['role_id'];
            $ret['department'] = $ret['dept_id'];
            $ret['leader'] = '';
            $ret['entry_time'] = 0;
            $ret['in_time'] = $ret['inert_time'];
            $ret['forbidden'] = $ret['status'] == 1 ? 0 : 1;
            $ret['birthday'] = $ret['birth_day'];
            $ret['is_modify'] = $ret['modify_pwd'];
            $ret['last_login'] = $ret['last_login_time'];
            $retAll[] = $ret;
        }
        return $retAll;
    }
            
}
