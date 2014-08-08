<?php

/**
 * @brief sso用到的常量
 * @author aozhongxu
 */

class SsoVars {
    
    public static $DEPARTMENT = array(
        30 => '呼叫中心客服经理',
        25 => '呼叫中心客服',
        26 => '店务秘书',
        27 => '店长',
        28 => '交易顾问',
        265 => '区域中心经理',
        341 => '连锁中心 - 招商专员',
    );
    
    /**
     * @brief 初始密码
     */
    public static function getInitPassword() {
        return md5('a123456');
    }
    
}
