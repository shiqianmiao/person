<?php
/**
 * 数据库工厂
 *
 * @author    戴志君 <dzjzmj@gmail.com>
 * @since     2013-2-20
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc      数据库工厂，初始化配置，并载入相应的数据库驱动类
 */

require_once(dirname(__FILE__) . '/exception/DbException.class.php');

/**
 * DataSource 数组库连接类
 * @package db
 */
class DbFactory {
    
    static private $_DB_INSTANCE = array();


    public static function create($type,$masterConfig,$slaveConfig) {
        $key = md5(serialize($type).serialize($masterConfig).serialize($slaveConfig));
        if (self::$_DB_INSTANCE[$key]) {
            return self::$_DB_INSTANCE[$key];
        }
		switch (strtolower($type)) {
            case 'oracle':
                include_once(dirname(__FILE__) . '/driver/DbOracle.class.php');
                $db = new DbOracle($masterConfig,$slaveConfig);
                break;
            default : // mysql
                include_once(dirname(__FILE__) . '/driver/DbMysql.class.php');
                $db = new DbMysql($masterConfig,$slaveConfig);
                break;
		}
        self::$_DB_INSTANCE[$key] = $db;
		return $db;
    }

}
