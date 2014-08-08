<?php

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';
class SsoGUModel2 extends BaseModel {

    protected function init() {
        $this->dbName         = DBConfig::DB_SSO;
        $this->tableName      = 'sso_GU';
        $this->dbMasterConfig = DBConfig::$SSO_MASTER;
        $this->dbSlaveConfig  = DBConfig::$SSO_SLAVE;
        $this->fieldTypes     = array(
            'group_id' => 'int',
            'user_id' => 'int',
        );
    }
}
