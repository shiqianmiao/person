<?php
/**
 * Created by PhpStorm.
 * User: chansun
 * Date: 15/1/12
 * Time: 下午4:51
 */

class MenuConfig {
    public static $CHANNEL_SSO  = 'sso';
    public static $CHANNEL_MBS = 'mbs';
    public static $CONFIG = array(
        'mbs' => array(
            'code'     => 'mbs',
            'name'     => '业务管理系统',
            'url'      => '/mbs/',
            'sub_menu' => array(
                'user' => array(
                    'code'     => 'mbs.user',
                    'name'     => '用户管理',
                    'url'      => '/mbs/user/',
                ),
                'user_add' => array(
                    'code'     => 'mbs.add_user',
                    'name'     => '添加',
                    'url'      => '/mbs/add_user/',
                ),
            ),
        ),
        'sso' => array(
            'code'     => 'mbs',
            'name'     => '单点登陆系统',
            'url'      => '/sso/',
        ),
    );
    public static function getConfig($channel){

    }
} 