<?php
/**
 * @brief 简介：model自动生成模板
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年3月13日 下午5:38:00
 */

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

class TplModel extends BaseModel {

    protected function init() {
        $this->dbName         = '{DB_NAME}';
        $this->tableName      = '{TABLE_NAME_TPL}';
        $this->dbMasterConfig = '{DB_MASTER_CONFIG}';
        $this->dbSlaveConfig  = '{DB_SLAVE_CONFIG}';
        $this->fieldTypes     = '{FILE_TYPES_TPL}';
    }

    public function getCount($params) {
        return $this->getOne('count(*)' , $params);
    }
}