<?php

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

/**
 * @author aozhongxu
 */

class SsoGUModel extends BaseModel {
    
    protected function init($id = '') {
        $this->dbName         = DBConfig::DB_SSO;
        $this->tableName      = 'sso_GU'; 
        $this->dbMasterConfig = DBConfig::$SSO_MASTER; 
        $this->dbSlaveConfig  = DBConfig::$SSO_SLAVE; 
        $this->fieldTypes     = array(
            'group_id' => 'INT',
            'user_id'  => 'INT',
        );
    }
    /**
     * 根据用户id 获取用户组id
     * @param <type> $id  用户id
     * @return <type> 用户组id
     */
    public function getSsoGroupForId($id){
        return $this->getOne('group_id', array(array('user_id', '=', $id)));
    }
    
}
