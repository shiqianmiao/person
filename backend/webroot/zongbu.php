<?php
/**
 * @author 范孝荣 <fanxr@273.cn>
 * @since 13-10-10
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @version $id$
 * @desc 
 */
set_time_limit(0);
define('APP_CONFIG_FILE_NAME', 'zbConfig.inc.php');
define('PERMISSION_APP_NAME', 'zb.bc');
define('SET_DEFAULT_PAGE', '/mbs_index/showIndex'); //业管首页

require_once dirname(__FILE__) . '/../config/bootstrap.php';
if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0')) {
    header("Content-type: text/html; charset=utf-8");
    $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
    echo '<h3>您好，为了您能更安全更快的使用本系统，请您<a href="http://chrome.360.cn/"><font size="18">下载使用</font></a>360极速安全浏览器，请在安装后访问'.$url.'，谢谢。</h3>';
    exit;
}
$routeParams = Bootstrap::getRouteParams();

Bootstrap::run();

exit;
