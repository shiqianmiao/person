<?php

/**
 * @brief 简易的权限管理
 */
class Permission {

    CONST SYSTEM_USER      = 1;      //系统管理员群组编号
    CONST CUSTOMER_SERVICE = 2;      //客服群组编号
    CONST FINANCE_USER     = 3;      //财务人员群组编号
    CONST MANAGER          = 4;      //经纪人
    CONST EDIT             = 5;      //编辑
    CONST SERVICE_MANAGER  = 6;      //客服 + 经纪人(但是没有账户管理权限)
    
    public static $groupName = array(
        '1' => '系统超级管理员',
        '2' => '总部客服人员',
        '3' => '总部财务人员',
        '4' => '总部客户经纪人',
        '5' => '总部编辑人员',
        '6' => '总部客户经纪人', //这个同事拥有经纪人 + 客服的权限。 暂时这样处理(但是没有账户管理权限)
    );

    public static function getCode($userName) {
        $group = self::getGroup($userName);
        $code = self::codeConfig($group);
        return $code;
    }
    
    public static function codeConfig($groupNum) {
        if (empty($groupNum)) {
            return array();
        }
        //第一个：bc.xxx为角色代表的code 第二个开始bc.center.xxx为一级菜单权限  center.xxx.xxx为二级菜单权限
        $codeGroup = [
            self::SYSTEM_USER      => ['bc.*'],
            self::CUSTOMER_SERVICE => ['bc.service','center.account', 'center.worker.list','bc.center.order', 'bc.center.account', 'bc.center.customer'],
            self::FINANCE_USER     => ['bc.finance'],
            self::MANAGER          => ['bc.manager', 'center.worker.list','center.orderList.list'],
            self::EDIT             => ['bc.edit', 'bc.finance', 'bc.center.help', 'bc.center.account'],
            self::SERVICE_MANAGER  => ['bc.manager', 'bc.service', 'bc.center.order', 'bc.center.account', 'center.worker.list','center.orderList.list'],
        ];
        if (array_key_exists($groupNum, $codeGroup)) {
            return $codeGroup[$groupNum];
        }
        return false;
    }
    
    public static function getGroup($userName) {
        $groupConf = [
            'water'       => self::SYSTEM_USER,
            'root'        => self::SYSTEM_USER,
            'miaoshiqian' => self::SYSTEM_USER,
            'agent'       => self::MANAGER,
            'bj20004'     => self::MANAGER,
            'bj20005'     => self::MANAGER,
            'bj20007'     => self::MANAGER,
            'bj20009'     => self::SERVICE_MANAGER,//客服 + 经纪人(但是没有账户管理权限)
            'bj20014'     => self::MANAGER,
            'bj20015'     => self::MANAGER,
            //'bj20016'     => self::MANAGER, 拿掉罗伟
            'service'     => self::CUSTOMER_SERVICE,
            'bj10008'     => self::CUSTOMER_SERVICE,
            'bj10013'     => self::CUSTOMER_SERVICE,
            'bj10018'     => self::CUSTOMER_SERVICE,
            'liyan'       => self::EDIT,
        ];
        if(array_key_exists($userName, $groupConf)) {
            return $groupConf[$userName];
        }
        return false;
    }
    
    public static function getGroupName($userName) {
        $group = self::getGroup($userName);
        return self::$groupName[$group];
    }
}