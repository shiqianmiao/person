<?php
/**
 * 基础配置文件
 *
 * @author    gaosk <gaoshikao@qq.com>
 * @since     2013-2-28
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc       基础配置文件
 */

define('DEBUG_STATUS', true);

if (DEBUG_STATUS) {
    define('CONF_PATH' , dirname(__FILE__) . '/debug');
    define('DISPLAY_ERROR_MBS_V3', true);
    define('DISPLAY_ERROR_WEB_V3', true);
    define('DISPLAY_ERROR_PAY_V3', true);
    define('DISPLAY_ERROR_AUCTION_V3', true);
} else {
    define('CONF_PATH' , dirname(__FILE__) . '/runtime');
    define('DISPLAY_ERROR_MBS_V3', false);
    define('DISPLAY_ERROR_WEB_V3', false);
    define('DISPLAY_ERROR_PAY_V3', false);
    define('DISPLAY_ERROR_AUCTION_V3', false);
}
///配置文件路径

define('FRAMEWORK_PATH' , CONF_PATH . '/../../framework');
///基础类库路径

define('API_PATH', CONF_PATH . '/../../api');
///api类库路径

define('DATA_PATH', CONF_PATH . '/../../data');
///数据缓存路径

define('STATIC_PATH', CONF_PATH . '/../../static');
///静态文件(image,css,js)路径

define('APP_SERV_PATH', CONF_PATH . '/../../app_serv');
///web api类库路径

define('COM_PATH' , dirname(__FILE__) . '/common');
///公共配置文件路径

define('CDC_PATH', CONF_PATH . '/../../cdc');
