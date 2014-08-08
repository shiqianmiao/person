<?php

/**
 * @brief 单点入口
 * @author aozhongxu
 */

require_once dirname(__FILE__) . '/bootstrap.php';
require_once FRAMEWORK_PATH . '/mvc/XhprofLog.class.php';
XhprofLog::beginXhprof();

$str = $_GET['_sys_url_path'];

$arr = explode('/', $str);

$module = $arr[0] ? $arr[0] : 'account';
$action = $arr[1] ? $arr[1] : 'login';

Dispatch::run($module, $action);
XhprofLog::logXhprof('sso');

