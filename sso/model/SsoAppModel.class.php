<?php

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

/**
 * @author aozhongxu
 */

class SsoAppModel extends BaseModel {
    
    protected function init($id = '') {
        $this->dbName         = DBConfig::DB_SSO;
        $this->tableName      = 'sso_app'; 
        $this->dbMasterConfig = DBConfig::$SSO_MASTER; 
        $this->dbSlaveConfig  = DBConfig::$SSO_SLAVE; 
        $this->fieldTypes     = array(
            'id'      => 'INT',
            'app'     => 'VARCHAR',
            'name'    => 'VARCHAR',
            'brief'   => 'VARCHAR',
            'in_time' => 'INT',
        );
    }
    
}
