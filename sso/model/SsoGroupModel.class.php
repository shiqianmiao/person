<?php

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

/**
 * @author aozhongxu
 */

class SsoGroupModel extends BaseModel {
    
    protected function init($id = '') {
        $this->dbName         = DBConfig::DB_SSO;
        $this->tableName      = 'sso_group'; 
        $this->dbMasterConfig = DBConfig::$SSO_MASTER; 
        $this->dbSlaveConfig  = DBConfig::$SSO_SLAVE; 
        $this->fieldTypes     = array(
            'id'    => 'INT',
            'name'  => 'VARCHAR',
            'brief' => 'VARCHAR',
            'code'  => 'VARCHAR',
        );
    }
    
}
