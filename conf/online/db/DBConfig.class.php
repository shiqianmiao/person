<?php
/**
 * @brief 简介：数据库配置类
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月13日 下午4:59:19
 */
class DBConfig {
    /**
     * 数据库名称
     */
    const DB_SSO = 'sso';
    const DB_BC = 'bc';
    const DB_LOG = 'log';
    
    /**
     * 主库
     */
    const COMMON_MASTER_HOST     = '218.244.141.149';
    const COMMON_MASTER_USERNAME = 'root';
    const COMMON_MASTER_PASSWD   = 'MSQ110mm';
    const COMMON_MASTER_PORT     = '3306';
    
    /**
     * 从库
     */
    const COMMON_SLAVE_HOST     = '218.244.141.149';
    const COMMON_SLAVE_USERNAME = 'root';
    const COMMON_SLAVE_PASSWD   = 'MSQ110mm';
    const COMMON_SLAVE_PORT     = '3306';
    
    
    public static $SERVER_MASTER = array(
            'host'      => self::COMMON_MASTER_HOST,
            'username'  => self::COMMON_MASTER_USERNAME,
            'password'  => self::COMMON_MASTER_PASSWD,
            'port'      => self::COMMON_MASTER_PORT,
    );
    
    public static $SERVER_SLAVE = array(
            'host'      => self::COMMON_SLAVE_HOST,
            'username'  => self::COMMON_SLAVE_USERNAME,
            'password'  => self::COMMON_SLAVE_PASSWD,
            'port'      => self::COMMON_SLAVE_PORT,
    );
    
}
