<?php
/**
 * 数据库配置类
 */
class DBConfig {

   /**
    * 数据库名称
    */
   //后台数据库
    const DB_BC = 'bc';
    const DB_SSO = 'sso';

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

    /**
     * sso数据库
     */
    const SSO_HOST = '218.244.141.149';
    const SSO_USERNAME = 'root';
    const SSO_PASSWD = 'MSQ110mm';
    const SSO_PORT = '3306';


    public static $SERVER_MASTER = array(
        'host'            => self::COMMON_MASTER_HOST,
        'username'  => self::COMMON_MASTER_USERNAME,
        'password'  => self::COMMON_MASTER_PASSWD,
        'port'            => self::COMMON_MASTER_PORT,
    );

    public static $SERVER_SLAVE = array(
        'host'      => self::COMMON_SLAVE_HOST,
        'username'  => self::COMMON_SLAVE_USERNAME,
        'password'  => self::COMMON_SLAVE_PASSWD,
        'port'      => self::COMMON_SLAVE_PORT,
    );

    public static $SSO_MASTER = array(
        'host'      => self::SSO_HOST,
        'username'  => self::SSO_USERNAME,
        'password'  => self::SSO_PASSWD,
        'port'      => self::SSO_PORT,
    );
    
    public static $SSO_SLAVE = array(
        'host'      => self::SSO_HOST,
        'username'  => self::SSO_USERNAME,
        'password'  => self::SSO_PASSWD,
        'port'      => self::SSO_PORT,
    );

//====================================================
   /**
    * 电话转接数据库
    */
    const DB_CT_PHONE       = 'ct';
    const CT_PHONE_HOST     = '192.168.5.14';//192.168.5.14
    const CT_PHONE_USERNAME = 'phone';
    const CT_PHONE_PASSWD   = 'tel_gototop_0591';
    const CT_PHONE_PORT     = '3306';
    
    const PAYFOR_HOST     = '192.168.5.31';//192.168.5.31
    const PAYFOR_USERNAME = 'pay_agent';
    const PAYFOR_PASSWD   = 'hahaha#0591';
    const PAYFOR_PORT     = '3306';
    
    /**
     * PV,UV统计库
     */
    const DB_TONJI = 'tongji';
    const TONGJI_HOST = '192.168.5.15';//192.168.5.14
    const TONJI_USERNAME = 'tongji_backend';
    const TONJI_PASSWD = 'tongji273';
    const TONJI_PORT = '3306';

    public static $TONGJI_MASTER = array(
            'host'      => self::TONGJI_HOST,
            'username'  => self::TONJI_USERNAME,
            'password'  => self::TONJI_PASSWD,
            'port'      => self::TONJI_PORT,
    );

    public static $SERVER_203 = array(
        'host' => '192.168.5.203',
        'username' => 'w273cn',
        'password' => 'w273cn_gototop_0591',
        'port' => 3306
    );

    public static $CT_PHONE_MASTER = array(
        'host'      => self::CT_PHONE_HOST,
        'username'  => self::CT_PHONE_USERNAME,
        'password'  => self::CT_PHONE_PASSWD,
        'port'      => self::CT_PHONE_PORT,
    );
    public static $CT_PHONE_SLAVE = array(
        'host'      => self::CT_PHONE_HOST,
        'username'  => self::CT_PHONE_USERNAME,
        'password'  => self::CT_PHONE_PASSWD,
        'port'      => self::CT_PHONE_PORT,
    );
    
    public static $PAYFOR_MASTER = array(
        'host'      => self::PAYFOR_HOST,
        'username'  => self::PAYFOR_USERNAME,
        'password'  => self::PAYFOR_PASSWD,
        'port'      => self::PAYFOR_PORT,
    );
     
    public static $PAYFOR_SLAVE = array(
        'host'      => self::PAYFOR_HOST,
        'username'  => self::PAYFOR_USERNAME,
        'password'  => self::PAYFOR_PASSWD,
        'port'      => self::PAYFOR_PORT,
    );
    
    public static $ZS_INTENT_MASTER = array(
        'host'      => self::COMMON_MASTER_HOST,
        'username'  => self::COMMON_MASTER_USERNAME,
        'password'  => self::COMMON_MASTER_PASSWD,
        'port'      => self::COMMON_MASTER_PORT,
    );
    public static $ZS_INTENT_SLAVE = array(
        'host'      => self::COMMON_SLAVE_HOST,
        'username'  => self::COMMON_SLAVE_USERNAME,
        'password'  => self::COMMON_SLAVE_PASSWD,
        'port'      => self::COMMON_SLAVE_PORT,
    );

    public static $PAYMENT_MASTER = array(
        'host'      => self::COMMON_MASTER_HOST,
        'username'  => 'pay_agent',
        'password'  => 'hahaha#0591',
        'port'      => '3306',
    );
    public static $PAYMENT_SLAVE = array(
        'host'      => self::COMMON_SLAVE_HOST,
        'username'  => 'pay_agent',
        'password'  => 'hahaha#0591',
        'port'      => '3306',
    );
}
