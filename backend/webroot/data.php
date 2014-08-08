<?php
/*
 * @desc 数据统计平台入口
 * @author郭朝辉
 * @since 2013-11-21
 */

set_time_limit(0);
define('APP_CONFIG_FILE_NAME', 'dataConfig.inc.php');
define('PERMISSION_APP_NAME', 'data');
define('SET_DEFAULT_PAGE', '/data_zs/Index'); 
//定义默认页面(这里是招商统计页面为默认页面，有不让它在menu里面显示，所以定义常量)

require_once dirname(__FILE__) . '/../config/bootstrap.php';

if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0')) {
    header("Content-type: text/html; charset=utf-8");
    $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
    echo '<h3>您好，为了您能更安全更快的使用本系统，请您<a href="http://chrome.360.cn/"><font size="18">下载使用</font></a>360极速安全浏览器，请在安装后访问'.$url.'，谢谢。</h3>';
    exit;
}

$routeParams = Bootstrap::getRouteParams();
Bootstrap::run();
