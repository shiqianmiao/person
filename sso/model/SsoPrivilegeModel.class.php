<?php

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

/**
 * @author aozhongxu
 */

class SsoPrivilegeModel extends BaseModel {
    
    protected function init($id = '') {
        $this->dbName         = DBConfig::DB_SSO;
        $this->tableName      = 'sso_privilege'; 
        $this->dbMasterConfig = DBConfig::$SSO_MASTER; 
        $this->dbSlaveConfig  = DBConfig::$SSO_SLAVE; 
        $this->fieldTypes     = array(
            'id'      => 'INT',
            'code'    => 'VARCHAR',
            'name'    => 'VARCHAR',
            'brief'   => 'VARCHAR',
            'in_time' => 'INT',
            'app'     => 'VARCHAR',
            'url'     => 'VARCHAR',
        );
    }
    
}
