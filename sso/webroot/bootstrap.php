<?php

/**
 * @brief sso项目引导文件
 * @author aozhongxu
 */
define('SSO_SITE', dirname(dirname(__FILE__)));
define('FRAMEWORK_PATH', SSO_SITE . '/../framework');
define('CONF_PATH', SSO_SITE . '/../conf/debug');
define('API_PATH', SSO_SITE . '/../api');
if (!defined('DEBUG_STATUS')) {
    define('DEBUG_STATUS', true);
}

// sso的框架不依赖v3
require_once SSO_SITE . '/core/Dispatch.class.php';
require_once SSO_SITE . '/core/Page.class.php';
require_once SSO_SITE . '/core/PhpTpl.class.php';

// 载入常用
require_once FRAMEWORK_PATH . '/util/http/RequestUtil.class.php';
require_once FRAMEWORK_PATH . '/util/http/ResponseUtil.class.php';

$display_errors = true;

if ($display_errors) { 
    error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
    ini_set('display_errors', 1); 
} else {
    error_reporting(0);
    ini_set('display_errors', 0); 
}
