<?php

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

/**
 * @author aozhongxu
 */

class SsoGPModel extends BaseModel {
    
    protected function init($id = '') {
        $this->dbName         = DBConfig::DB_SSO;
        $this->tableName      = 'sso_GP'; 
        $this->dbMasterConfig = DBConfig::$SSO_MASTER; 
        $this->dbSlaveConfig  = DBConfig::$SSO_SLAVE; 
        $this->fieldTypes     = array(
            'group_id'     => 'INT',
            'privilege_id' => 'INT',
        );
    }
    
}
