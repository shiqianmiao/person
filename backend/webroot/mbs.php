<?php
/*
 * @desc 新业管入口文件
 * @author 缪石乾 <miaosq@273.cn>
 * @since 2013-9-2
 */
set_time_limit(0);
$module = !empty($_GET['_sys_url_path']) ? $_GET['_sys_url_path'] : '';
if (!empty($module)) {
    $module = explode('/', $module);
    $module = $module['0'];
}

if ($module == 'shop') {
    //业管里面的店铺管理模块
    define('APP_CONFIG_FILE_NAME', 'shopConfig.inc.php');
    define('PERMISSION_APP_NAME', 'mbs.bc');
    define('SET_DEFAULT_PAGE', '/shop/showIndex'); //定义默认页面
    define('SET_MBS_MODULE', 'shop'); //设置业管模块
} else {
    define('APP_CONFIG_FILE_NAME', 'mbsConfig.inc.php');
    define('PERMISSION_APP_NAME', 'mbs.bc');
    define('SET_DEFAULT_PAGE', '/mbs_index/showIndex'); //定义默认页面
}

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
