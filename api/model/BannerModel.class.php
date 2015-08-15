<?php
/**
 * @brief 简介：model自动生成模板
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年3月13日 下午5:38:00
 */

require_once FRAMEWORK_PATH . '/util/db/BaseModel.class.php';
require_once CONF_PATH . '/db/DBConfig.class.php';

class BannerModel extends BaseModel {
    /**
     * @breif status枚举常量配置
     */
    const STATUS_NORMAL = 1;

    protected function init() {
        $this->dbName         = DBConfig::DB_BC;
        $this->tableName      = 'banner';
        $this->dbMasterConfig = DBConfig::$SERVER_MASTER;
        $this->dbSlaveConfig  = DBConfig::$SERVER_SLAVE;
        $this->fieldTypes     = array(
            'id' => 'int', // 自增id
            'title1' => 'varchar', // banner标题1
            'title2' => 'varchar', // banner标题2
            'brief' => 'varchar', // banner简要介绍
            'path' => 'varchar', //图片路径
            'status' => 'int', // banner状态. 0:无效状态、1：正常显示、-1：删除
            'weight' => 'int', // 排序权重、值越到、权重越高. 
            'create_time' => 'int', // 添加时间
            'update_time' => 'int', // 更新时间
            'create_user' => 'int', //添加人员的后台账号
        );
    }

    public function getCount($params) {
        return $this->getOne('count(*)' , $params);
    }
}